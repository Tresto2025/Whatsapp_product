<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The inbox: listing conversations, reading a thread, and replying — where an
 * outbound send both hits Meta and is persisted into the transcript.
 */
class InboxTest extends TestCase
{
    use RefreshDatabase;

    private function actingInTenant(Tenant $tenant): User
    {
        $user = User::factory()->forTenant($tenant)->tenantAdmin()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_the_inbox_lists_this_tenants_conversations(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $mine = Conversation::factory()->forTenant($tenant)->create();
        $mine->contact->update(['profile_name' => 'Aarav Mine']);

        $this->get(route('conversations.index'))
            ->assertOk()
            ->assertSee('Aarav Mine');
    }

    public function test_you_cannot_open_another_tenants_conversation(): void
    {
        $mine = Tenant::factory()->create();
        $theirs = Tenant::factory()->create();

        $foreign = Conversation::factory()->forTenant($theirs)->create();

        $this->actingInTenant($mine);

        $this->get(route('conversations.show', $foreign))->assertNotFound();
    }

    public function test_opening_a_thread_clears_the_unread_count(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $conversation = Conversation::factory()->forTenant($tenant)->create(['unread_count' => 3]);
        Message::factory()->forConversation($conversation)->inbound()->create();

        $this->get(route('conversations.show', $conversation))->assertOk();

        $this->assertSame(0, $conversation->fresh()->unread_count);
    }

    public function test_a_reply_is_sent_to_meta_and_saved_to_the_transcript(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response([
            'messages' => [['id' => 'wamid.OUT1']],
        ], 200)]);

        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $contact = Contact::factory()->forTenant($tenant)->create(['wa_id' => '919812345678']);
        $conversation = Conversation::factory()->forTenant($tenant)->create([
            'contact_id' => $contact->id,
            'whatsapp_account_id' => $account->id,
        ]);

        $this->post(route('conversations.reply', $conversation), ['body' => 'Yes, 7pm works.'])
            ->assertRedirect(route('conversations.show', $conversation))
            ->assertSessionHas('success');

        $message = Message::withoutGlobalScopes()
            ->where('direction', Message::OUT)
            ->first();

        $this->assertNotNull($message);
        $this->assertSame('Yes, 7pm works.', $message->body);
        $this->assertSame('wamid.OUT1', $message->meta_message_id);
        $this->assertSame(Message::STATUS_SENT, $message->status);
        $this->assertSame($tenant->id, $message->tenant_id);

        Http::assertSent(fn ($request) => str_contains($request->url(), $account->phone_number_id.'/messages')
            && $request['text']['body'] === 'Yes, 7pm works.');
    }

    public function test_a_send_meta_rejects_is_recorded_as_failed(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response([
            'error' => ['message' => 'Message failed to send because more than 24 hours have passed.'],
        ], 400)]);

        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $contact = Contact::factory()->forTenant($tenant)->create();
        $conversation = Conversation::factory()->forTenant($tenant)->create([
            'contact_id' => $contact->id,
            'whatsapp_account_id' => $account->id,
        ]);

        $this->post(route('conversations.reply', $conversation), ['body' => 'too late'])
            ->assertSessionHasErrors('body');

        $message = Message::withoutGlobalScopes()->where('direction', Message::OUT)->first();

        $this->assertNotNull($message, 'the failed send is still recorded in the transcript');
        $this->assertSame(Message::STATUS_FAILED, $message->status);
        $this->assertNotNull($message->error);
    }

    public function test_a_reply_requires_a_body(): void
    {
        $tenant = Tenant::factory()->create();
        $this->actingInTenant($tenant);

        $conversation = Conversation::factory()->forTenant($tenant)->create();

        $this->post(route('conversations.reply', $conversation), ['body' => ''])
            ->assertSessionHasErrors('body');
    }
}
