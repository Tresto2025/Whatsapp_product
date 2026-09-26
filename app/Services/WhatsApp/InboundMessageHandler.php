<?php

namespace App\Services\WhatsApp;

use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappAccount;
use App\Services\Chatbot\FlowMatcher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Turns one Meta webhook payload into rows: contacts, conversations, messages,
 * and delivery-status updates.
 *
 * Runs inside the owning tenant's context, so the tenant global scope fills in
 * tenant_id and keeps every lookup inside that workspace.
 */
class InboundMessageHandler
{
    public function __construct(private FlowMatcher $flowMatcher)
    {
    }

    public function handle(WhatsappAccount $account, array $payload): void
    {
        foreach (data_get($payload, 'entry', []) as $entry) {
            foreach (data_get($entry, 'changes', []) as $change) {
                $value = data_get($change, 'value', []);

                // Delivery receipts arrive on their own webhook, separate from
                // messages. Without handling these there is no "who read it".
                foreach (data_get($value, 'statuses', []) as $status) {
                    $this->applyStatus($status);
                }

                $profiles = $this->profileNames($value);

                foreach (data_get($value, 'messages', []) as $message) {
                    $this->storeInbound($account, $message, $profiles);
                }
            }
        }
    }

    /** contacts[] carries the WhatsApp profile name, keyed by wa_id. */
    private function profileNames(array $value): array
    {
        $names = [];

        foreach (data_get($value, 'contacts', []) as $contact) {
            $waId = data_get($contact, 'wa_id');
            if ($waId) {
                $names[Contact::normaliseWaId($waId)] = data_get($contact, 'profile.name');
            }
        }

        return $names;
    }

    private function storeInbound(WhatsappAccount $account, array $message, array $profiles): void
    {
        $from = data_get($message, 'from');

        if (!$from) {
            return;
        }

        $waId = Contact::normaliseWaId($from);
        $metaId = data_get($message, 'id');

        // Meta retries webhooks, so the same message can arrive twice.
        if ($metaId && Message::where('meta_message_id', $metaId)->exists()) {
            return;
        }

        $contact = Contact::firstOrCreate(
            ['wa_id' => $waId],
            ['profile_name' => $profiles[$waId] ?? null]
        );

        if (!$contact->profile_name && isset($profiles[$waId])) {
            $contact->profile_name = $profiles[$waId];
        }

        $sentAt = ($ts = data_get($message, 'timestamp')) ? Carbon::createFromTimestamp((int) $ts) : now();

        $contact->last_inbound_at = $sentAt;
        $contact->save();

        $conversation = Conversation::firstOrCreate(
            ['contact_id' => $contact->id, 'whatsapp_account_id' => $account->id],
            ['status' => Conversation::STATUS_OPEN]
        );

        $body = $this->extractBody($message);

        Message::create([
            'conversation_id' => $conversation->id,
            'direction' => Message::IN,
            'type' => data_get($message, 'type', 'text'),
            'body' => $body,
            'payload' => $message,
            'meta_message_id' => $metaId,
            'status' => Message::STATUS_DELIVERED, // it reached us
            'sent_at' => $sentAt,
        ]);

        $conversation->forceFill([
            'last_message_at' => $sentAt,
            'unread_count' => $conversation->unread_count + 1,
            'status' => Conversation::STATUS_OPEN,
        ])->save();

        $this->flowMatcher->handle($account, $contact, $conversation, $body);
    }

    /**
     * The readable text of a message, whatever kind it is — so the inbox and
     * any keyword matching have one field to work with.
     */
    private function extractBody(array $message): ?string
    {
        return data_get($message, 'text.body')
            ?? data_get($message, 'button.text')
            ?? data_get($message, 'interactive.button_reply.title')
            ?? data_get($message, 'interactive.list_reply.title')
            ?? data_get($message, 'image.caption')
            ?? data_get($message, 'video.caption')
            ?? data_get($message, 'document.caption')
            ?? null;
    }

    private function applyStatus(array $status): void
    {
        $metaId = data_get($status, 'id');
        $state = data_get($status, 'status');

        if (!$metaId || !$state) {
            return;
        }

        $message = Message::where('meta_message_id', $metaId)->first();

        if (!$message) {
            // A receipt can beat our own record of the send, or belong to a
            // message sent before this platform existed.
            Log::info('WhatsApp status for an unknown message', ['meta_message_id' => $metaId]);
            return;
        }

        $at = ($ts = data_get($status, 'timestamp')) ? Carbon::createFromTimestamp((int) $ts) : now();

        if ($message->advanceStatus($state, $at)) {
            if ($state === Message::STATUS_FAILED) {
                $message->error = data_get($status, 'errors.0.title') ?? data_get($status, 'errors.0.message');
            }
            $message->save();

            $this->mirrorToCampaignRecipient($message);
        }
    }

    /**
     * A campaign send is one message; carry its delivery status onto that
     * recipient so a campaign report can answer "who read it" per person.
     */
    private function mirrorToCampaignRecipient(Message $message): void
    {
        if (!in_array($message->status, [
            Message::STATUS_SENT, Message::STATUS_DELIVERED,
            Message::STATUS_READ, Message::STATUS_FAILED,
        ], true)) {
            return;
        }

        CampaignRecipient::where('message_id', $message->id)->update([
            'status' => $message->status,
            'updated_at' => now(),
        ]);
    }
}
