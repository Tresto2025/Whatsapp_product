<?php

namespace Tests\Feature;

use App\Jobs\ProcessInboundWhatsAppMessage;
use App\Models\ChatbotFlow;
use App\Models\FlowStep;
use App\Models\FlowTrigger;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The MVP chatbot: a tenant admin configures a keyword -> single reply, and an
 * inbound message matching that keyword gets an automatic outbound reply.
 */
class ChatbotAutoReplyTest extends TestCase
{
    use RefreshDatabase;

    private function actingInTenant(Tenant $tenant): User
    {
        $user = User::factory()->forTenant($tenant)->tenantAdmin()->create();
        $this->actingAs($user);

        return $user;
    }

    private function textFlow(Tenant $tenant, string $keyword, string $reply, bool $active = true, bool $exact = false): ChatbotFlow
    {
        $flow = ChatbotFlow::create(['tenant_id' => $tenant->id, 'name' => 'Greeting', 'is_active' => $active]);

        FlowTrigger::create([
            'tenant_id' => $tenant->id,
            'flow_id' => $flow->id,
            'match_type' => FlowTrigger::TYPE_KEYWORD,
            'value' => $keyword,
            'exact_match' => $exact,
        ]);

        FlowStep::create([
            'tenant_id' => $tenant->id,
            'flow_id' => $flow->id,
            'order' => 0,
            'action_type' => FlowStep::ACTION_SEND_TEXT,
            'payload' => ['body' => $reply],
        ]);

        return $flow;
    }

    private function inbound(WhatsappAccount $account, string $text, string $metaId = 'wamid.IN'): array
    {
        return ['entry' => [['changes' => [['field' => 'messages', 'value' => [
            'metadata' => ['phone_number_id' => $account->phone_number_id],
            'contacts' => [['wa_id' => '919812345678', 'profile' => ['name' => 'Priya Sharma']]],
            'messages' => [[
                'from' => '919812345678',
                'id' => $metaId,
                'timestamp' => '1758400000',
                'type' => 'text',
                'text' => ['body' => $text],
            ]],
        ]]]]]];
    }

    private function deliver(WhatsappAccount $account, array $payload): void
    {
        (new ProcessInboundWhatsAppMessage($account->id, $payload))
            ->handle(app(\App\Tenancy\TenantManager::class), app(\App\Services\WhatsApp\InboundMessageHandler::class));
    }

    public function test_a_matching_keyword_triggers_an_automatic_text_reply(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $this->textFlow($tenant, 'hi', 'Hello! How can we help?');

        $this->deliver($account, $this->inbound($account, 'hi there'));

        $reply = Message::withoutGlobalScopes()->where('direction', Message::OUT)->first();

        $this->assertNotNull($reply, 'the auto-reply was sent and recorded');
        $this->assertSame('Hello! How can we help?', $reply->body);
        $this->assertSame('wamid.OUT', $reply->meta_message_id);
        $this->assertSame(Message::STATUS_SENT, $reply->status);

        Http::assertSent(fn ($request) => str_contains($request->url(), $account->phone_number_id.'/messages')
            && $request['text']['body'] === 'Hello! How can we help?');
    }

    public function test_a_non_matching_message_gets_no_reply(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $this->textFlow($tenant, 'hi', 'Hello!');

        $this->deliver($account, $this->inbound($account, 'what are your hours?'));

        $this->assertSame(0, Message::withoutGlobalScopes()->where('direction', Message::OUT)->count());
    }

    public function test_a_paused_flow_does_not_reply(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $this->textFlow($tenant, 'hi', 'Hello!', active: false);

        $this->deliver($account, $this->inbound($account, 'hi'));

        $this->assertSame(0, Message::withoutGlobalScopes()->where('direction', Message::OUT)->count());
    }

    public function test_exact_match_does_not_fire_on_a_partial_message(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $this->textFlow($tenant, 'hi', 'Hello!', exact: true);

        $this->deliver($account, $this->inbound($account, 'hi there'));

        $this->assertSame(0, Message::withoutGlobalScopes()->where('direction', Message::OUT)->count());
    }

    public function test_a_flow_only_fires_for_its_own_tenant(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $otherTenant = Tenant::factory()->create();
        $this->textFlow($otherTenant, 'hi', 'Hello from tenant B!');

        $myTenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($myTenant)->create();

        $this->deliver($account, $this->inbound($account, 'hi'));

        $this->assertSame(0, Message::withoutGlobalScopes()->where('direction', Message::OUT)->count());
    }

    public function test_a_template_reply_with_placeholders_is_skipped_safely(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OUT']]], 200)]);

        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();

        $flow = ChatbotFlow::create(['tenant_id' => $tenant->id, 'name' => 'Missing template', 'is_active' => true]);
        FlowTrigger::create([
            'tenant_id' => $tenant->id, 'flow_id' => $flow->id,
            'match_type' => FlowTrigger::TYPE_KEYWORD, 'value' => 'hi', 'exact_match' => false,
        ]);
        FlowStep::create([
            'tenant_id' => $tenant->id, 'flow_id' => $flow->id, 'order' => 0,
            'action_type' => FlowStep::ACTION_SEND_TEMPLATE, 'payload' => ['template_id' => 999999],
        ]);

        $this->deliver($account, $this->inbound($account, 'hi'));

        $this->assertSame(0, Message::withoutGlobalScopes()->where('direction', Message::OUT)->count());
        Http::assertNothingSent();
    }

    public function test_the_chatbot_pages_render(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        WhatsappTemplate::factory()->forAccount($account)->create(['name' => 'welcome_plain', 'variables' => []]);
        $this->textFlow($tenant, 'hi', 'Hello!');

        $this->get(route('tenant.chatbot.index'))->assertOk()->assertSee('Hello!');
        $this->get(route('tenant.chatbot.create'))->assertOk()->assertSee('welcome_plain');
    }

    public function test_agents_cannot_manage_auto_replies(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingAs(User::factory()->forTenant($tenant)->create());

        $this->get(route('tenant.chatbot.index'))->assertForbidden();
        $this->get(route('tenant.chatbot.create'))->assertForbidden();
    }

    public function test_a_tenant_admin_can_create_a_text_auto_reply(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $this->post(route('tenant.chatbot.store'), [
            'name' => 'Greeting',
            'keyword' => 'hi',
            'reply_type' => 'text',
            'reply_body' => 'Hello! How can we help?',
        ])->assertRedirect(route('tenant.chatbot.index'))->assertSessionHas('success');

        $flow = ChatbotFlow::withoutGlobalScopes()->first();

        $this->assertNotNull($flow);
        $this->assertSame('Greeting', $flow->name);
        $this->assertTrue($flow->is_active);
        $this->assertSame($tenant->id, $flow->tenant_id);
        $this->assertSame('hi', $flow->triggers->first()->value);
        $this->assertSame('Hello! How can we help?', $flow->steps->first()->payload['body']);
    }

    public function test_creating_an_auto_reply_requires_a_keyword(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $this->post(route('tenant.chatbot.store'), [
            'name' => 'Greeting',
            'keyword' => '',
            'reply_type' => 'text',
            'reply_body' => 'Hello!',
        ])->assertSessionHasErrors('keyword');

        $this->assertSame(0, ChatbotFlow::withoutGlobalScopes()->count());
    }

    public function test_a_template_with_placeholders_cannot_be_used_for_an_auto_reply(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $template = WhatsappTemplate::factory()->forAccount($account)->create(['variables' => ['1']]);

        $this->post(route('tenant.chatbot.store'), [
            'name' => 'Greeting',
            'keyword' => 'hi',
            'reply_type' => 'template',
            'template_id' => $template->id,
        ])->assertSessionHasErrors('template_id');

        $this->assertSame(0, ChatbotFlow::withoutGlobalScopes()->count());
    }

    public function test_toggling_pauses_and_reactivates_a_flow(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);
        $flow = $this->textFlow($tenant, 'hi', 'Hello!');

        $this->post(route('tenant.chatbot.toggle', $flow))->assertRedirect();
        $this->assertFalse($flow->fresh()->is_active);

        $this->post(route('tenant.chatbot.toggle', $flow))->assertRedirect();
        $this->assertTrue($flow->fresh()->is_active);
    }

    public function test_you_cannot_see_another_tenants_auto_replies(): void
    {
        $theirs = Tenant::factory()->create();
        $this->textFlow($theirs, 'hi', 'Hello from tenant B!');

        $mine = Tenant::factory()->create();
        $this->actingInTenant($mine);

        $this->get(route('tenant.chatbot.index'))
            ->assertOk()
            ->assertDontSee('Hello from tenant B!');
    }

    public function test_deleting_a_flow_removes_its_trigger_and_step(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);
        $flow = $this->textFlow($tenant, 'hi', 'Hello!');

        $this->delete(route('tenant.chatbot.destroy', $flow))->assertRedirect();

        $this->assertSame(0, ChatbotFlow::withoutGlobalScopes()->count());
        $this->assertSame(0, FlowTrigger::withoutGlobalScopes()->count());
        $this->assertSame(0, FlowStep::withoutGlobalScopes()->count());
    }
}
