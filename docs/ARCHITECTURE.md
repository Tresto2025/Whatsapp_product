# Architecture Plan — Multi-Tenant WhatsApp Chatbot SaaS

## Context

**Why:** Today the repo (`Tresto2025/Whatsapp_product`) is a **single-tenant** Laravel 10 app.
It talks to **one** Meta WhatsApp number whose credentials live in `.env`, replies to
wake-up messages via a Meta-side template, and manages doctors/appointments for one
business. There is no notion of separate customers.

**Goal:** Turn it into an **end-to-end multi-tenant product** where:
- A **Super Admin** provisions and oversees many **tenants** (client businesses).
- Each **tenant** logs into their own admin panel, connects **their own** Meta WhatsApp
  account (access token, phone number id, WABA id, app secret), and configures **their own**
  chatbot flows/templates (e.g. "book appointment", "connect to team").
- Every inbound message, request, and appointment is captured and visible **per tenant**
  on the platform, with analytics.

**Decisions (confirmed with user):**
1. **Tenancy:** Shared database, row-level isolation via a `tenant_id` column + a global
   Eloquent scope. (Not DB-per-tenant.)
2. **WhatsApp onboarding:** Phased — **manual credential entry first** (MVP), **Meta
   Embedded Signup later** once approved as a Tech Provider.
3. **Chatbots:** **No-code, data-driven flow builder** (tenants self-configure triggers → actions).
4. **Approach:** **Evolve the existing Laravel codebase** (reuse WhatsApp/appointment/payment logic).

---

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

### 4. No-code chatbot flow engine
- Tables:
  - **`chatbot_flows`**: `id, tenant_id, name, is_active, default_reply, timestamps`.
  - **`flow_triggers`**: `id, flow_id, match_type (keyword|template|any|button), value`.
  - **`flow_steps`**: `id, flow_id, order, action_type (send_text|send_template|
    book_appointment|connect_to_team|collect_input|handoff), payload (json)`.
  - **`whatsapp_templates`**: `id, tenant_id, meta_template_name, category, status, body,
    variables (json)` — synced from Meta per tenant.
- **`FlowEngine`** service: given an inbound message + tenant, find the matching trigger,
  walk steps, execute actions (reuse existing appointment-booking logic), persist state.
- **Conversation state** table **`chat_sessions`** already exists (`ChatSessions` model) —
  extend with `tenant_id` + `current_flow_id` + `state (json)`.

### 5. Conversation & request logging (platform visibility)
- **`conversations`** (`tenant_id, wa_contact, last_message_at, status`) and **`messages`**
  (`conversation_id, direction, type, body, template_name, meta_message_id, status,
  timestamps`) — every inbound/outbound stored per tenant.
- Appointments gain `tenant_id`; existing appointment UI becomes tenant-scoped.
- **Analytics** views per tenant: message volume, appointments booked, template usage,
  response rates. Super admin sees cross-tenant rollups.

### 6. Panels & routing
- **Super Admin panel** (`/superadmin/*`, `super_admin` middleware): tenant CRUD, plan/
  subscription mgmt, provisioning, global metrics, impersonate-tenant.
- **Tenant Admin panel** (existing dashboard, tenant-scoped): connect WhatsApp, build/enable
  flows, manage templates, view conversations & appointments, analytics, team/staff, billing.
- **Onboarding flow:** signup → tenant created → connect WhatsApp → sync templates →
  build/enable a flow → go live.
- Keep the marketing frontend; make it tenant-agnostic.

### 7. Billing (reuse existing Razorpay/wallet)
- Add **`plans`** and **`subscriptions`** (per tenant). Reuse `RazorpayController`,
  `WalletController`, `MessagePlans`, `SmsBalance` — re-scope them to `tenant_id`.
- Meter per-tenant message usage against plan limits.

---

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

**Phase 0 — Hygiene & safety (1–2 days).** Confirm repo is private; remove DB dump + dead
files + stray zips; `env()`→`config()`; guard `/clear-cache` & `admin/message-price`; add
webhook HMAC verification (single-tenant, as a stepping stone). Ship independently.

**Phase 1 — Tenancy core (1 wk).** `tenants` table, `BelongsToTenant` trait + global scope,
spatie roles, `ResolveTenant`/`EnsureSuperAdmin` middleware, migrate existing data as
"Tenant #1". Reconstruct baseline migrations from the dump.

**Phase 2 — Per-tenant WhatsApp (1 wk).** `whatsapp_accounts` (encrypted creds),
`WhatsAppClient` service, manual onboarding UI, multi-tenant webhook routing + per-account
signature verification, queued inbound processing.

**Phase 3 — Flow engine + templates (1.5 wks).** Flow/trigger/step tables, `FlowEngine`,
template sync from Meta, no-code builder UI, conversation/message logging.

**Phase 4 — Panels & analytics (1 wk).** Super Admin panel (tenant CRUD, plans, impersonate),
tenant analytics dashboards, appointment/request views re-scoped per tenant.

**Phase 5 — Billing + Embedded Signup (1 wk+).** Plans/subscriptions, usage metering, Meta
Embedded Signup onboarding.

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

## Open questions / risks

- **Schema reconstruction:** rebuilding migrations from the 20 MB dump is the biggest unknown;
  budget time to diff dump schema vs. models.
- **Meta compliance:** Embedded Signup requires Tech Provider/BSP approval (weeks, external
  dependency) — that's why it's phased last.
- **Token storage:** per-tenant access tokens are sensitive; rely on Laravel encryption +
  ensure `APP_KEY` management and DB-at-rest security.
- **Queue infra:** need a queue worker (database/Redis) in production for async webhook
  processing — currently `sync`.
