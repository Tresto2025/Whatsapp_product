# Architecture Plan — Multi-Tenant WhatsApp Chatbot SaaS

## Context

**What this is.** A multi-tenant WhatsApp Business platform — the same category as AiSensy,
Wati or Interakt. A company signs up, connects the WhatsApp Cloud API account it created on
Meta, and runs its customer messaging from one dashboard: templates, broadcasts, an
automated chatbot, and the record of who responded and what they wanted.

**It is not a doctor or clinic product.** The repository began as a single-tenant
appointment-booking app for one clinic, and that vocabulary is still everywhere in the code:
`doctor_id` on nine tables, 44 `/doctor/*` routes, 48 views, ~900 references. Treat it as
reference material and as one vertical's worth of working WhatsApp and booking logic — not
as the domain model. See **Legacy clinic vertical**.

**What a tenant does, end to end:**

1. Sign up; get a workspace.
2. Create their WhatsApp Business account and message templates on Meta (utility, marketing,
   authentication) — this happens on Meta, not here.
3. Paste their Cloud API credentials in; the platform verifies and stores them encrypted.
4. Sync their approved templates into the platform.
5. Build a chatbot with no code: triggers (keyword, button tap, template reply) to steps
   (send text, send template, collect input, record a booking, hand off to a human).
6. Broadcast a template to a segment of their contacts.
7. Watch what came back: who it was delivered to, who read it, who replied, who booked, who
   asked about which service.

**What the platform has to own** — as opposed to what lives in the tenant's Meta account:

| Platform owns | Why |
|---|---|
| **Contacts** | Meta has no CRM; segments, tags and opt-in state are ours |
| **Conversations + messages** | Meta retains no transcript; the transcript is the product |
| **Templates** (mirrored) | Flows and campaigns are built against them, with approval state |
| **Campaigns** | Per-recipient delivery/read/reply state for a template send |
| **Flows** | The no-code chatbot definition |
| **Responses** | The structured outcome a flow captured — booking, interest, lead |
| **Analytics** | Rolled up from all of the above |

**Decisions (confirmed):** shared database with `tenant_id` row isolation; manual credential
entry first and Meta Embedded Signup later; no-code data-driven flows; evolve this codebase
rather than start over.

**The vocabulary correction matters most for one thing.** "Appointments" is not the domain.
It is one kind of **response** that one kind of flow records. A restaurant's flow records a
table booking, a gym's a trial signup, a dealership's a test-drive request. Outcomes must be
stored generically or every new vertical needs a schema change.
## Current-state facts (verified in code)

- **Framework:** Laravel 10, PHP 8.1, Sanctum, DomPDF, simple-qrcode, Twilio SDK, maatwebsite/excel.
- **Roles:** `users.role` integer — `1` = admin, `2` = doctor. Checked ad-hoc
  (`app/Http/Controllers/DashboardController.php:16`), **no** middleware/policy gating.
  Most routes sit behind bare `auth` only.
- **WhatsApp creds:** single set read via `env()` in ~18 places
  (`WHATSAPP_TOKEN`, `PHONE_NUMBER_ID`), e.g. `BroadcastMessagesController.php:20-21`,
  `WebhookController.php:22-23`, `DoctorAppointmentController.php:272`.
- **Webhook:** `routes/api.php` → `WhatsAppController@webhook` / `WebhookController@receive`.
  **No** per-account routing and **no** `X-Hub-Signature-256` HMAC check. Verify token is
  **hardcoded** `'my_verify_token'` (`WebhookController.php:34`).
- **Data model:** `Appointments.doctor_id` → `User`. `User` is both admin and doctor.
  No `tenant_id` anywhere.
- **Schema source of truth:** the committed 20 MB `infosuzn_tatkal2.sql` dump — only **4**
  migrations exist for ~24 models. Schema is NOT reproducible from code.
- **Queue:** `QUEUE_CONNECTION=sync` (everything runs inline; broadcast/send is synchronous).
- **Cleanliness debt:** ~30 committed `*-old` / `*_backup_*` files, two controllers
  misfiled in `app/Models/` (`BlogPostController.php`, `BlogCategoryController.php`),
  stray zips, DB dump in git.

---

## Target architecture

### 1. Tenancy foundation
- Add **`tenants`** table: `id, name, slug, status, plan_id, owner_user_id, timestamps`.
- Add nullable **`tenant_id`** FK to every tenant-owned table (users, appointments,
  broadcast_messages, sms_balance, doctor_services, blog_*, conversations, messages, etc.).
- **`BelongsToTenant`** trait + a **global scope** that filters every query by the current
  tenant, and auto-fills `tenant_id` on create. Resolve "current tenant" from the
  authenticated user (web) or from the matched WhatsApp account (webhook).
- **Super Admin bypass:** super admin requests run without the scope (see roles).
- Use **spatie/laravel-permission** for roles: `super_admin`, `tenant_admin`, `tenant_staff`
  (maps the old `role` 1/2). Replace ad-hoc `role ==` checks with middleware/policies.
- Recommended package to reduce boilerplate: **`stancl/tenancy` (single-DB mode)** OR a
  lightweight hand-rolled trait. Plan uses the hand-rolled trait to keep control and avoid
  over-abstracting; revisit stancl if we later want domain-per-tenant.

### 2. Per-tenant WhatsApp connections
- New table **`whatsapp_accounts`**: `id, tenant_id, waba_id, phone_number_id,
  display_phone_number, access_token (encrypted), app_secret (encrypted), verify_token,
  webhook_status, connection_status, provider (manual|embedded), meta_business_id, timestamps`.
  Use Laravel **encrypted casts** for token/app_secret.
- A **`WhatsAppClient`** service (wraps Graph API send/template calls) is constructed
  **from a `whatsapp_accounts` row**, not from `env()`. This is the central refactor: every
  current `env('WHATSAPP_TOKEN')` / `PHONE_NUMBER_ID` call site is replaced by
  `WhatsAppClient::forTenant($tenant)` / `forAccount($account)`.
- **Manual onboarding (MVP):** tenant admin form to paste creds → validate by calling Graph
  API (`GET /{phone_number_id}`) → store encrypted → mark connected → show the single shared
  webhook URL + verify token to paste into Meta.
- **Embedded Signup (phase 2):** Facebook JS SDK flow → exchange code → auto-populate the
  same `whatsapp_accounts` row. Same downstream code path.

### 3. Multi-tenant webhook routing (critical)
- **One shared webhook URL** `POST /api/whatsapp/webhook`.
- On inbound: read `entry[].changes[].value.metadata.phone_number_id` → look up
  `whatsapp_accounts` → resolve tenant → **verify `X-Hub-Signature-256` HMAC** using that
  account's `app_secret` → set tenant context → dispatch to that tenant's flow engine.
- Reject unmatched `phone_number_id` or bad signature. Replace hardcoded verify token with
  the per-account `verify_token`.
- Push heavy work to a **queued job** (`ProcessInboundWhatsAppMessage`) — move off
  `QUEUE_CONNECTION=sync` to `database` (or Redis) so webhook returns fast.

### 4. Contacts (who the tenant is talking to)

Meta gives us a phone number on each inbound message and nothing else. Everything a tenant
needs to segment and target lives here.

- **`contacts`**: `id, tenant_id, wa_id (E.164), name, profile_name, email, attributes (json),
  opted_in_at, opted_out_at, last_inbound_at, last_outbound_at, timestamps`.
  Unique on `(tenant_id, wa_id)` — the same person can be a contact of two tenants and those
  are different rows.
- **`tags`** + **`contact_tag`**: free-form labels a tenant applies ("vip", "interested-in-
  brunch"), used as campaign segments and set by flow steps.
- Opt-out is a platform concern: a contact who sends STOP must be excluded from marketing
  campaigns automatically, regardless of what the flow says.

### 5. Conversations and messages (the transcript is the product)

- **`conversations`**: `id, tenant_id, contact_id, whatsapp_account_id, status
  (open|snoozed|closed), assigned_user_id, last_message_at, unread_count, timestamps`.
- **`messages`**: `id, tenant_id, conversation_id, direction (in|out), type
  (text|image|template|interactive|…), body, template_id, payload (json), meta_message_id,
  status (queued|sent|delivered|read|failed), error, sent_at, timestamps`.
  Indexed on `meta_message_id` because delivery receipts arrive later and are matched by it.
- Meta sends **status webhooks** (sent → delivered → read, or failed) separately from
  messages. The webhook handler must fan out on `value.statuses[]` as well as
  `value.messages[]`; without that there is no "who read it", which is half of what the
  tenant is buying.
- The existing `chat_sessions` table stays as flow state; it is not the transcript.

### 6. Templates (mirrored from Meta)

- **`whatsapp_templates`**: `id, tenant_id, whatsapp_account_id, meta_template_id, name,
  language, category (utility|marketing|authentication), status (approved|pending|rejected|
  paused), body, header_type, variables (json), synced_at, timestamps`.
- Synced by calling `GET /{waba_id}/message_templates` with the tenant's token. Templates are
  **created and approved on Meta**, never here — the platform mirrors them so flows and
  campaigns can be built against a known-approved list, and so a send is not attempted
  against a rejected template.
- Meta also pushes `message_template_status_update` webhooks; handling those keeps status
  fresh without polling.

### 7. Campaigns (broadcast, with per-recipient truth)

- **`campaigns`**: `id, tenant_id, whatsapp_account_id, template_id, name, segment (json),
  scheduled_at, started_at, completed_at, status, counts (json), timestamps`.
- **`campaign_recipients`**: `id, campaign_id, contact_id, message_id, status, failed_reason`.
  This is what makes "who received / read / replied" answerable per campaign rather than as a
  single aggregate number.
- Sending is queued and rate-limited per account; Meta throttles, and a synchronous loop over
  thousands of contacts (what `BroadcastMessagesController` does today) will not survive.
- Marketing campaigns must respect opt-out; utility ones follow Meta's own rules.

### 8. No-code chatbot flow engine

- **`chatbot_flows`**: `id, tenant_id, name, is_active, default_reply, timestamps`.
- **`flow_triggers`**: `id, flow_id, match_type (keyword|button|template_reply|any), value`.
- **`flow_steps`**: `id, flow_id, parent_step_id, order, action_type (send_text|send_template|
  ask_question|save_attribute|add_tag|record_response|handoff|end), payload (json)`.
- **`FlowEngine`**: given an inbound message and a tenant, match a trigger, walk the steps,
  persist position in `chat_sessions`, and emit messages through `WhatsAppClient`.
- The existing clinic booking logic in `WebhookController` is the reference implementation of
  what one flow should be able to express: ask a question, offer a list, validate, confirm.

### 9. Responses (what analytics actually counts)

This is the generic replacement for "appointments", and the piece the current schema is
missing entirely.

- **`responses`**: `id, tenant_id, contact_id, conversation_id, flow_id, type
  (booking|enquiry|interest|lead|custom), status (new|confirmed|cancelled|done), scheduled_for,
  data (json), timestamps`.
- A `record_response` flow step writes one. A clinic's booking flow writes
  `type=booking, scheduled_for=…`; a restaurant's writes `type=booking` with a table size in
  `data`; a gym's "interested in a trial" writes `type=interest`.
- Everything the tenant asked to see — who booked, who replied, who is interested in which
  service — is a query over `responses` joined to `contacts`, not a per-vertical table.

### 10. Analytics

Per tenant: messages in/out over time, delivery and read rates, template performance,
campaign funnels (sent → delivered → read → replied → response recorded), flow completion and
drop-off, response counts by type and status, busiest hours. Super admin sees the same rolled
up across tenants plus per-tenant usage against plan.

Read models matter here: counting millions of `messages` rows live will not hold up, so daily
per-tenant rollup tables are part of this phase, not an optimisation afterwards.

### 11. Panels & routing

- **Super admin** (`/superadmin/*`): tenant CRUD and provisioning, suspend/resume, plans and
  subscriptions, cross-tenant metrics, per-tenant usage, impersonate.
- **Tenant admin**: inbox (conversations), contacts, templates, campaigns, flow builder,
  responses, analytics, team, WhatsApp connection, billing.
- **Agent/staff**: inbox and contacts only — no credentials, no billing.
- Onboarding: sign up → connect WhatsApp → sync templates → import contacts → build a flow →
  go live.

### 12. Billing

`plans` and `subscriptions` per tenant; meter messages and campaign sends against plan limits;
reuse the existing Razorpay and wallet code, re-scoped to `tenant_id`. Meta bills the tenant
directly for conversations, so platform billing is for the software, not the messages —
worth stating in the UI so tenants are not surprised by two invoices.

### Legacy clinic vertical

The doctor/appointment code (`/doctor/*`, `doctor_id` on nine tables, `DoctorController`,
`DoctorAppointmentController`, the booking flow inside `WebhookController`) is the original
single-tenant product. It is tenant-scoped and works, so it keeps running for the existing
clinic, but it is **not** the platform's domain model and should not be extended.

Three options, to decide before Phase 3 starts:

1. **Park it (recommended).** Build the generic core beside it; leave the clinic on the old
   screens until flows + responses can express what it does, then retire them. No disruption,
   some duplication for a while.
2. **Generalise in place.** Rename `doctor_id` to `user_id`, relabel the UI. ~900 references,
   nine tables, real risk to a working tenant, and it still leaves "appointments" as a
   first-class table rather than a response type.
3. **Strip it now.** Fastest route to a clean product, breaks the existing clinic.

Recommendation is (1): the booking flow is the best available spec for what the flow engine
must support, so it is worth keeping runnable while that engine is built.
## Target schema (clean build)

The legacy schema is not carried forward. It was reconstructed from the production dump in
Phase 1 so the existing clinic stayed reproducible, and that job is done — but 20 of its 29
tables describe a clinic, not a messaging platform. The product is built on the tables below
instead.

### Kept from the current build

`tenants`, `whatsapp_accounts`, and Laravel's own `migrations`, `jobs`, `failed_jobs`,
`password_reset_tokens`, `personal_access_tokens`, `sessions`.

`users` is rebuilt: the current one carries 33 columns of clinic profile (consultation
timings, slot gaps, PAN/GST, service template ids). A platform user needs
`id, tenant_id, name, email, password, role, status, last_seen_at, timestamps` and nothing
more. Per-vertical fields belong in `contacts.attributes`, not on the operator account.

### Dropped

`appointments`, `doctor_service`, `doctor_timings`, `services`, `sms_balance`, `sms_logs`,
`sms_payments`, `wallet_balance`, `wallet_payments`, `payments`, `message_plans`,
`message_prices`, `broadcast_messages`, `trainer_profession`, `trainer_skills`, `posts`,
`categories`, `cities`, `states`, `countries`.

Appointments become `responses`. Broadcasts become `campaigns` + `campaign_recipients`.
SMS/wallet balances become `subscriptions` + usage counters. The geo tables (148k cities)
were reference data for a clinic directory and have no place here.

### New core

| Table | Holds |
|---|---|
| `contacts` | `tenant_id, wa_id, name, profile_name, email, attributes json, opted_in_at, opted_out_at, last_inbound_at, last_outbound_at` — unique `(tenant_id, wa_id)` |
| `tags`, `contact_tag` | tenant-defined labels; campaign segments and flow outputs |
| `conversations` | `tenant_id, contact_id, whatsapp_account_id, status, assigned_user_id, last_message_at, unread_count` |
| `messages` | `tenant_id, conversation_id, direction, type, body, template_id, payload json, meta_message_id, status, error, sent_at` — indexed on `meta_message_id` for status callbacks |
| `whatsapp_templates` | mirrored from Meta: `meta_template_id, name, language, category, status, body, header_type, variables json, synced_at` |
| `campaigns` | `template_id, name, segment json, scheduled_at, started_at, completed_at, status, counts json` |
| `campaign_recipients` | `campaign_id, contact_id, message_id, status, failed_reason` — per-recipient truth |
| `chatbot_flows` | `name, is_active, default_reply` |
| `flow_triggers` | `flow_id, match_type (keyword\|button\|template_reply\|any), value` |
| `flow_steps` | `flow_id, parent_step_id, order, action_type, payload json` |
| `flow_runs` | live position of one contact through one flow: `flow_id, contact_id, conversation_id, current_step_id, state json, status` — replaces `chat_sessions` |
| `responses` | the outcome a flow captured: `contact_id, conversation_id, flow_id, type, status, scheduled_for, data json` |
| `daily_stats` | per-tenant per-day rollups so analytics never counts raw `messages` live |
| `plans`, `subscriptions`, `usage_counters` | billing and metering |

Roughly 20 purpose-built tables in place of 29 inherited ones, with every one of them
tenant-scoped from the first migration rather than gaining `tenant_id` afterwards.

### What this changes about the work

- **Phase 1's guarded migrations can go.** `Schema::hasTable()` guards existed only to be
  safe against a dump-loaded database. A fresh database needs none of that, and
  `migrate:fresh` becomes the normal path rather than a thing we tiptoe around.
- **`tenant_id` stops being nullable.** It was nullable so legacy rows could be backfilled.
  On a clean build it is `NOT NULL` with a foreign key, which makes cross-tenant leakage a
  database error rather than something the global scope has to catch.
- **The clinic app stops working**, because its tables are gone. That is the decision to
  confirm below, not something to discover later.

## Files to change / add (representative, not exhaustive)

**New (foundation):**
- `database/migrations/*` — `tenants`, add `tenant_id` to all tenant tables, `whatsapp_accounts`,
  `chatbot_flows`, `flow_triggers`, `flow_steps`, `whatsapp_templates`, `conversations`,
  `messages`, `plans`, `subscriptions`. **Also baseline-migrate the existing schema** from the
  SQL dump (reconstruct migrations so the DB is reproducible).
- `app/Models/Tenant.php`, `WhatsappAccount.php`, `ChatbotFlow.php`, `FlowTrigger.php`,
  `FlowStep.php`, `WhatsappTemplate.php`, `Conversation.php`, `Message.php`, `Plan.php`,
  `Subscription.php`.
- `app/Models/Concerns/BelongsToTenant.php` (trait + global scope).
- `app/Services/WhatsApp/WhatsAppClient.php`, `app/Services/Chatbot/FlowEngine.php`.
- `app/Jobs/ProcessInboundWhatsAppMessage.php`.
- `app/Http/Middleware/ResolveTenant.php`, `EnsureSuperAdmin.php`.
- `app/Http/Controllers/SuperAdmin/*`, `app/Http/Controllers/Tenant/WhatsAppConnectionController.php`,
  `Tenant/FlowBuilderController.php`, `Tenant/TemplateController.php`, `Tenant/ConversationController.php`.
- `config/services.php` — WhatsApp/Twilio/Razorpay config keys.

**Refactor (reuse existing logic):**
- Replace `env('WHATSAPP_TOKEN')`/`env('PHONE_NUMBER_ID')` everywhere with `WhatsAppClient`
  built from a tenant account: `WebhookController.php`, `Api/WhatsAppController.php`,
  `BroadcastMessagesController.php`, `DoctorBroadcastMessagesController.php`,
  `DoctorAppointmentController.php`.
- Add `tenant_id` + `BelongsToTenant` to `User`, `Appointments`, `BroadcastMessages`,
  `SmsBalance`, `DoctorService`, `Post`, `Category`, `ChatSessions`, etc.
- `routes/web.php` (add superadmin + tenant groups, guard `/clear-cache` and
  `admin/message-price`, convert GET-deletes to DELETE), `routes/api.php` (harden webhook).
- `DashboardController.php` — role logic → spatie roles + policies.

**Cleanup (pre-req, low risk):**
- Delete ~30 `*-old`/`*_backup_*` files, stray zips, and the `infosuzn_tatkal2.sql` dump from
  git (keep a copy outside the repo). Move the two misfiled controllers out of `app/Models/`.

---

## Phased delivery

Phases 0–2 are complete; see **Implementation progress**. The remainder was re-cut once the
product was clarified as a general WhatsApp Business platform rather than a clinic tool.

**Phase 0 — Hygiene & safety. Done.** Repo private, DB dump and dead files removed,
`env()`→`config()`, webhook HMAC verification, dangerous routes guarded.

**Phase 1 — Tenancy core. Done.** `tenants`, `BelongsToTenant` + global scope, `TenantManager`,
`ResolveTenant`/`EnsureSuperAdmin`, native roles, baseline migrations reconstructed from the
dump, isolation tests.

**Phase 2 — Per-tenant WhatsApp. Done.** `whatsapp_accounts` with encrypted credentials,
`WhatsAppClient` built per account, manual onboarding UI with live credential verification,
webhook routing by `phone_number_id` with per-account signature checks, queued inbound
processing, self-serve tenant signup.

**Phase 3 — Contacts and the transcript. Partly done.** `contacts`, `tags`, `conversations`,
`messages`. Persist every inbound message; persist every outbound send from `WhatsAppClient`;
handle Meta's `statuses[]` webhooks so delivered/read/failed land on the right message. Build
the inbox UI. **This is the foundation for everything the tenant wants to see** — without the
transcript there is nothing to analyse, so it comes before flows.

**Phase 4 — Templates and campaigns (1–1.5 wks).** Sync templates from
`GET /{waba_id}/message_templates`, handle template status webhooks, template list UI.
`campaigns` + `campaign_recipients`, segment picker over contacts/tags, queued rate-limited
sending, per-recipient delivery state, opt-out handling. Replaces the current synchronous
broadcast controllers.

**Phase 5 — Flow engine and responses (1.5–2 wks).** `chatbot_flows`, `flow_triggers`,
`flow_steps`, `responses`; `FlowEngine` walking steps against `chat_sessions` state; no-code
builder UI. Port the clinic booking flow onto the engine as the proving case — if the engine
can express it, it can express a restaurant booking or a gym trial signup.

**Phase 6 — Analytics and panels (1–1.5 wks).** Daily per-tenant rollups; tenant dashboards
(volume, delivery/read rates, template and campaign performance, flow drop-off, responses by
type); super admin panel (tenant CRUD, provisioning, suspend, impersonate, cross-tenant
metrics and usage).

**Phase 7 — Billing and Embedded Signup (1 wk+, partly external).** `plans`, `subscriptions`,
usage metering against limits, Razorpay/wallet re-scoped. Meta Embedded Signup once Tech
Provider approval lands — external dependency, so it stays last.

**Sequencing note.** Flows were originally Phase 3. They moved after contacts/transcript and
templates because a flow step that sends a template needs a synced template list, and a flow
that records a response needs a contact and a conversation to attach it to. Building flows
first would mean building them twice.

---
## Verification

- **Migrations:** `php artisan migrate:fresh --seed` builds the full schema from code (no
  dependence on the SQL dump); seed a super admin + 2 demo tenants.
- **Tenant isolation (automated tests):** create Tenant A & B, assert Tenant A user cannot
  read/write Tenant B appointments/conversations via the global scope; assert super admin sees
  both. `php artisan test`.
- **Webhook routing (local):** POST sample Meta payloads with two different `phone_number_id`
  values → each lands in the correct tenant's conversation; a bad `X-Hub-Signature-256` is
  rejected; unknown `phone_number_id` is rejected.
- **Flow engine:** simulate an inbound "book appointment" keyword for Tenant A → correct
  template/reply sent via Tenant A's `WhatsAppClient`, appointment row created with
  `tenant_id = A`, message logged.
- **End-to-end (staging):** connect a real Meta test number to a demo tenant, send a WhatsApp
  message, confirm the configured flow responds and the conversation/appointment appear in that
  tenant's panel only.
- **Regression:** existing single-tenant appointment/broadcast flows still work as "Tenant #1".

---

## Implementation progress

- **Phase 0 — done.** Hygiene & security: DB dump + dead files removed, `env()`→`config()`,
  webhook HMAC verification, `/clear-cache` & `admin/message-price` guarded, misfiled
  controllers moved.
- **Phase 1 — done.** Tenancy core landed: `tenants` table + guarded `tenant_id`
  migration with default-tenant backfill, `Tenant` model, `BelongsToTenant` trait +
  `TenantScope` global scope, `TenantManager` singleton, `ResolveTenant` middleware (in the
  web group), role constants/helpers on `User`, `EnsureSuperAdmin` using them, and
  `TenancySeeder` (default tenant + super admin).
  - **Deviation from plan:** roles are implemented natively (integer `users.role` +
    constants/helpers: 0 super admin, 1 tenant admin, 2 tenant staff) instead of
    spatie/laravel-permission, to avoid a hard `composer require` dependency in the current
    environment. Can be swapped to spatie later without changing call sites (helpers stay).
  - **Baseline migrations reconstructed.** 21 new `create_*` migrations plus a rewritten
    `create_users_table` reproduce the production schema from the `infosuzn_tatkal2.sql`
    dump, so `migrate:fresh` now builds the whole database from code. Verified by diffing
    `information_schema` against the dump: every dump column is reproduced; the only
    differences are the intentional tenancy additions (`tenants` table + `tenant_id` on 15
    tables) and `cities_old`, a dead table that is deliberately not recreated.
    - The stock `create_users_table` described the Laravel skeleton (`name`), not the real
      33-column table the app reads (`first_name`/`last_name`, `role`, scheduling and tax
      fields). It has been replaced with the real schema.
    - Every baseline migration is guarded with `Schema::hasTable()` so it is a no-op against
      the legacy production database, which already holds these tables but has only the four
      original migrations recorded in its `migrations` table.
    - Types are reproduced faithfully rather than "fixed" (e.g. `doctor_id int` pointing at
      `users.id bigint`, and `users.email` carrying no unique index because production has
      none and `deleted_at` allows re-registration). Changing them is a separate decision.
  - **Isolation tests added.** `tests/Feature/TenantIsolationTest.php` covers auto-filled
    `tenant_id`, scoped reads, cross-tenant read/update/delete being blocked, per-tenant
    counts, and super-admin bypass seeing every tenant.
  - **Test-support fixes required by the real schema:** `UserFactory` now produces
    `first_name`/`last_name`, a role and a tenant (tenant-less users are rejected by
    `ResolveTenant`); a `TenantFactory` was added; `RegisteredUserController` wrote a `name`
    column that does not exist, so it now splits the submitted name into first/last.

- **Phase 2 — done.** Per-tenant WhatsApp.
  - `whatsapp_accounts` table: one Meta number per row, owned by a tenant.
    `phone_number_id` is unique platform-wide because it is the inbound routing key.
    `access_token` and `app_secret` use encrypted casts and are in `$hidden`, so they are
    ciphertext in a dump and cannot leak into a view or JSON response.
  - `WhatsAppClient` (`app/Services/WhatsApp/`) is always constructed from an account row.
    `current()` resolves the active tenant's default connected number and falls back to the
    platform credentials, so the legacy single-tenant flow keeps working until it is
    onboarded. It uses Laravel's HTTP client, which made the send path testable.
  - Every `curl` block in `WebhookController`, `BroadcastMessagesController`,
    `DoctorBroadcastMessagesController` and `DoctorAppointmentController` now goes through
    the client; no controller reads WhatsApp credentials any more.
  - **Webhook routing:** `POST /api/webhook` reads
    `entry[].changes[].value.metadata.phone_number_id`, resolves the owning account, verifies
    `X-Hub-Signature-256` with *that account's* secret, and dispatches
    `ProcessInboundWhatsAppMessage`, which runs the existing handler inside the tenant's
    context. Unknown numbers 404; bad or missing signatures 403.
  - The old 770-line `receive()` became `handleInbound(array $data)` unchanged; only its
    request parsing and signature check moved out.
  - `verify()` matches the handshake token against `whatsapp_accounts.verify_token` (the
    hardcoded platform token is still accepted for the legacy number) and flips
    `webhook_status` to verified.
  - Manual onboarding UI at `/tenant/whatsapp`: credentials are proven with
    `GET /{phone_number_id}` before the row is marked connected, so a typo fails at the form
    rather than silently on the first message.
  - Blog scoping gap from Phase 1 closed: `posts` and `categories` now carry `tenant_id`
    and use `BelongsToTenant`.

  **Two bugs found while building this, both fixed:**
  - `ResolveTenant` sat *after* `SubstituteBindings` in the web middleware group, so
    route-model binding resolved before any tenant context existed — one tenant could bind
    and delete another tenant's row by id. It now runs before binding, and a test covers it.
  - `WebhookController::send()` logged the access token in plaintext
    (`Log::info("TOKEN VALUE: ".$this->token)`). Removed.

  **Deliberately not done:** `Api/WhatsAppController@webhook` (`/api/whatsapp/webhook`) is
  the *Twilio* webhook — it reads `From`/`Body` form fields, not Meta's JSON — so it is a
  separate integration and is untouched by this phase.

### Removed stock scaffolding

`tests/Feature/ProfileTest.php` was unmodified Laravel Breeze scaffolding exercising
`/profile` GET/PATCH/DELETE. This application replaced those with `doctor/profile` routes
and has no account-deletion route, so all five cases 404'd — they covered nothing and could
not catch a regression. The file was removed; if Breeze-style profile management is ever
wanted, write tests against `ProfileController` instead.

With it gone, `php artisan test` is green.

### Clean-schema cutover (done)

The legacy schema was dropped rather than migrated. `whatsapp_platform` is built from 11
purpose-written migrations: tenants, users, whatsapp_accounts, contacts/tags,
conversations/messages, whatsapp_templates, campaigns, the flow tables, responses, and
plans/subscriptions/daily_stats. `tenant_id` is `NOT NULL` with a foreign key everywhere
except platform super admins, so a cross-tenant write fails at the database rather than
relying on the global scope alone.

Removed with it: 23 clinic models, 23 controllers, 48 views, the 2,034-line themed layout and
the doctor registration API. `users` went from 33 columns to 12.

Two things worth recording, both found during the cutover:

- `NewPasswordController` stored every reset password in plaintext in a `show_password`
  column. Column and code are both gone.
- `CustomResetPassword` referenced `title` and `last_name`; replaced with the framework's own
  notification rather than carrying clinic fields forward.

**Phase 3 status.** The inbound half is built and tested: `InboundMessageHandler` turns a
webhook into contact, conversation and message rows, deduplicates Meta's retries, extracts a
readable body from text, button, list and caption messages, and applies `statuses[]` receipts
under a forward-only rule so a late `sent` cannot overwrite a recorded `read`.

The inbox and outbound half is now built too. `OutboundMessageSender` sends a reply through
`WhatsAppClient::forAccount()` and records it as a `Message` row in the same step — a failed
send is written as `status=failed` with Meta's error rather than dropped, so the thread shows
it. `ConversationController` lists conversations, renders a thread (clearing the unread badge
on open), and replies; it is reachable by agents and admins alike, and route-model binding
inherits the tenant scope so one workspace cannot open another's thread. The reply form warns
when the 24-hour customer-service window has closed, since a free-text reply outside it will be
rejected by Meta. Covered by `InboxTest` (listing, cross-tenant 404, unread clearing, a
persisted send, a recorded failure, validation) plus `Contact`/`Conversation`/`Message`
factories added for it. **Still to do in Phase 3:** contact import (CSV) and the surrounding
contacts UI.

**Phase 4 status — templates done, campaigns next.** The `WhatsappTemplate` model and a
one-way sync are built: `WhatsAppClient::fetchTemplates()` pages `GET /{waba_id}/message_templates`,
and `TemplateSyncService` flattens Meta's component array (header/body/footer/buttons) into
columns, extracts the positional `{{n}}` variables, maps status/category, and upserts keyed by
`(account, name, language)` so a re-sync updates rather than duplicates. `TemplateController`
(admin-gated) lists the mirror and triggers a sync across the tenant's connected numbers.
Covered by `TemplateSyncTest`. Still to do in Phase 4: `campaigns` + `campaign_recipients`, a
segment picker over contacts/tags, queued rate-limited sending with per-recipient delivery
state, and opt-out handling.

## Open questions / risks

- **Schema reconstruction — resolved.** Baseline migrations were rebuilt from the dump and
  verified column-by-column against it; `migrate:fresh --seed` builds the database from code.
- **Meta compliance:** Embedded Signup requires Tech Provider/BSP approval (weeks, external
  dependency) — that's why it's phased last.
- **Token storage — implemented, but `APP_KEY` is now critical.** Per-tenant tokens are
  encrypted with `APP_KEY`. Rotating or losing it makes every stored credential
  undecryptable and every tenant has to reconnect, so back it up with the database.
- **Queue infra — action required before production.** Inbound webhooks now dispatch
  `ProcessInboundWhatsAppMessage`. The default stays `sync` (inline, correct, but Meta waits
  for the whole flow). Switching `QUEUE_CONNECTION` to `database` without also running
  `php artisan queue:work` would queue every message and process none — change both together.
- **Templates are still hardcoded.** Broadcast sends name Meta templates inline
  (`doctor_update_notification`, `new_doctor_message`, …) and assume each tenant's Meta
  account has a template of that exact name. Phase 3's `whatsapp_templates` sync replaces
  this; until then, onboarding a tenant means replicating those templates on their account.
