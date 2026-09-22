<?php

namespace App\Services\WhatsApp;

use App\Models\Conversation;
use App\Models\Message;
use RuntimeException;

/**
 * Sends an outbound message on a conversation AND records it in the transcript.
 *
 * WhatsAppClient is pure transport; this is where a send becomes a persisted
 * Message row so the inbox, delivery receipts and analytics all see it. A row
 * is written whether Meta accepts or rejects the send, so a failure is visible
 * in the thread rather than silently dropped.
 */
class OutboundMessageSender
{
    /**
     * @throws RuntimeException when the conversation has no number able to send.
     */
    public function sendText(Conversation $conversation, string $body): Message
    {
        $account = $conversation->account;

        if (!$account || !$account->isUsable()) {
            throw new RuntimeException('This conversation has no connected WhatsApp number to send from.');
        }

        $contact = $conversation->contact;
        $result = WhatsAppClient::forAccount($account)->sendText($contact->wa_id, $body);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'direction' => Message::OUT,
            'type' => 'text',
            'body' => $body,
            'meta_message_id' => $result['message_id'] ?? null,
            'status' => $result['ok'] ? Message::STATUS_SENT : Message::STATUS_FAILED,
            'error' => $result['ok'] ? null : $result['error'],
            'sent_at' => now(),
        ]);

        if ($result['ok']) {
            $conversation->forceFill([
                'last_message_at' => now(),
                'status' => Conversation::STATUS_OPEN,
            ])->save();

            $contact->forceFill(['last_outbound_at' => now()])->save();
        }

        return $message;
    }
}
