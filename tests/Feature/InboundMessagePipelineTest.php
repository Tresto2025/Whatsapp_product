<?php

namespace Tests\Feature;

use App\Jobs\ProcessInboundWhatsAppMessage;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Turning a Meta webhook into the transcript: contacts, conversations,
 * messages, and the delivery receipts that arrive separately afterwards.
 */
class InboundMessagePipelineTest extends TestCase
{
    use RefreshDatabase;

    private WhatsappAccount $account;

    protected function setUp(): void
    {
        parent::setUp();
        $this->account = WhatsappAccount::factory()->create();
    }

    private function inbound(string $text = 'hello', string $metaId = 'wamid.A', string $from = '919812345678'): array
    {
        return ['entry' => [['changes' => [['field' => 'messages', 'value' => [
            'metadata' => ['phone_number_id' => $this->account->phone_number_id],
            'contacts' => [['wa_id' => $from, 'profile' => ['name' => 'Priya Sharma']]],
            'messages' => [[
                'from' => $from,
                'id' => $metaId,
                'timestamp' => '1758400000',
                'type' => 'text',
                'text' => ['body' => $text],
            ]],
        ]]]]]];
    }

    private function statuses(array $statuses): array
    {
        return ['entry' => [['changes' => [['field' => 'messages', 'value' => [
            'metadata' => ['phone_number_id' => $this->account->phone_number_id],
            'statuses' => $statuses,
        ]]]]]];
    }

    private function deliver(array $payload): void
    {
        (new ProcessInboundWhatsAppMessage($this->account->id, $payload))
            ->handle(app(\App\Tenancy\TenantManager::class), app(\App\Services\WhatsApp\InboundMessageHandler::class));
    }

    public function test_an_inbound_message_creates_contact_conversation_and_message(): void
    {
        $this->deliver($this->inbound('Do you have a table for 4?'));

        $contact = Contact::withoutGlobalScopes()->first();
        $conversation = Conversation::withoutGlobalScopes()->first();
        $message = Message::withoutGlobalScopes()->first();

        $this->assertSame('919812345678', $contact->wa_id);
        $this->assertSame('Priya Sharma', $contact->profile_name);
        $this->assertSame($this->account->tenant_id, $contact->tenant_id);

        $this->assertSame($contact->id, $conversation->contact_id);
        $this->assertSame(1, $conversation->unread_count);

        $this->assertSame(Message::IN, $message->direction);
        $this->assertSame('Do you have a table for 4?', $message->body);
    }

    public function test_a_replayed_webhook_does_not_duplicate_the_message(): void
    {
        $payload = $this->inbound('hello', 'wamid.SAME');

        $this->deliver($payload);
        $this->deliver($payload);   // Meta retries anything it thinks failed

        $this->assertSame(1, Message::withoutGlobalScopes()->count());
        $this->assertSame(1, Contact::withoutGlobalScopes()->count());
    }

    public function test_a_second_message_reuses_the_same_conversation(): void
    {
        $this->deliver($this->inbound('first', 'wamid.1'));
        $this->deliver($this->inbound('second', 'wamid.2'));

        $this->assertSame(1, Conversation::withoutGlobalScopes()->count());
        $this->assertSame(2, Message::withoutGlobalScopes()->count());
        $this->assertSame(2, Conversation::withoutGlobalScopes()->first()->unread_count);
    }

    public function test_delivery_receipts_advance_the_message_status(): void
    {
        $this->deliver($this->inbound('hi', 'wamid.R'));

        $this->deliver($this->statuses([['id' => 'wamid.R', 'status' => 'read', 'timestamp' => '1758400100']]));

        $message = Message::withoutGlobalScopes()->first();

        $this->assertSame(Message::STATUS_READ, $message->status);
        $this->assertNotNull($message->read_at);
    }

    public function test_a_late_receipt_cannot_move_a_status_backwards(): void
    {
        $this->deliver($this->inbound('hi', 'wamid.R'));
        $this->deliver($this->statuses([['id' => 'wamid.R', 'status' => 'read', 'timestamp' => '1758400100']]));

        // Meta can deliver receipts out of order.
        $this->deliver($this->statuses([['id' => 'wamid.R', 'status' => 'sent', 'timestamp' => '1758400050']]));

        $this->assertSame(Message::STATUS_READ, Message::withoutGlobalScopes()->first()->status);
    }

    public function test_a_failure_receipt_records_the_reason(): void
    {
        $this->deliver($this->inbound('hi', 'wamid.F'));

        $this->deliver($this->statuses([[
            'id' => 'wamid.F',
            'status' => 'failed',
            'timestamp' => '1758400100',
            'errors' => [['title' => 'Message undeliverable']],
        ]]));

        $message = Message::withoutGlobalScopes()->first();

        $this->assertSame(Message::STATUS_FAILED, $message->status);
        $this->assertSame('Message undeliverable', $message->error);
    }

    public function test_a_button_reply_is_readable_in_the_transcript(): void
    {
        $payload = $this->inbound();
        data_set($payload, 'entry.0.changes.0.value.messages.0.type', 'interactive');
        data_set($payload, 'entry.0.changes.0.value.messages.0.text', null);
        data_set($payload, 'entry.0.changes.0.value.messages.0.interactive.button_reply.title', 'Book a table');

        $this->deliver($payload);

        $this->assertSame('Book a table', Message::withoutGlobalScopes()->first()->body);
    }

    public function test_messages_land_in_the_owning_tenant(): void
    {
        $other = WhatsappAccount::factory()->create();

        $this->deliver($this->inbound('for tenant A'));

        $contact = Contact::withoutGlobalScopes()->first();

        $this->assertSame($this->account->tenant_id, $contact->tenant_id);
        $this->assertNotSame($other->tenant_id, $contact->tenant_id);
    }
}
