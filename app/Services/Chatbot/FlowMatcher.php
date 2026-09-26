<?php

namespace App\Services\Chatbot;

use App\Models\ChatbotFlow;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\FlowStep;
use App\Models\Message;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use App\Services\WhatsApp\WhatsAppClient;
use Illuminate\Support\Facades\Log;

/**
 * MVP chatbot: matches an inbound message's text against the tenant's active
 * flow triggers and, on a match, sends that flow's single reply step.
 *
 * Deliberately single-turn — trigger -> one immediate reply. No branching, no
 * per-contact state (`flow_runs` is schema for that later phase). Runs inside
 * the tenant context `ProcessInboundWhatsAppMessage` already establishes, so
 * every query here is naturally scoped to the right tenant.
 */
class FlowMatcher
{
    public function handle(WhatsappAccount $account, Contact $contact, Conversation $conversation, ?string $body): void
    {
        $flow = $this->matchFlow($body);

        if (!$flow) {
            return;
        }

        $step = $flow->steps->sortBy('order')->first();

        if (!$step) {
            return;
        }

        $this->executeStep($account, $contact, $conversation, $step);
    }

    private function matchFlow(?string $body): ?ChatbotFlow
    {
        return ChatbotFlow::where('is_active', true)
            ->with('triggers')
            ->orderBy('priority')
            ->orderBy('id')
            ->get()
            ->first(fn (ChatbotFlow $flow) => $flow->triggers->contains(fn (
                $trigger
            ) => $trigger->matches($body)));
    }

    private function executeStep(WhatsappAccount $account, Contact $contact, Conversation $conversation, FlowStep $step): void
    {
        $client = WhatsAppClient::forAccount($account);

        $result = match ($step->action_type) {
            FlowStep::ACTION_SEND_TEXT => $client->sendText($contact->wa_id, (string) ($step->payload['body'] ?? '')),
            FlowStep::ACTION_SEND_TEMPLATE => $this->sendTemplate($client, $contact, $step),
            default => null,
        };

        if ($result === null) {
            return;
        }

        $this->recordOutbound($conversation, $step, $result);
    }

    private function sendTemplate(WhatsAppClient $client, Contact $contact, FlowStep $step): ?array
    {
        $template = WhatsappTemplate::find($step->payload['template_id'] ?? null);

        if (!$template) {
            Log::warning('Chatbot flow step points at a missing or foreign template', ['flow_step_id' => $step->id]);

            return null;
        }

        return $client->sendTemplate($contact->wa_id, $template->name, [], $template->language);
    }

    private function recordOutbound(Conversation $conversation, FlowStep $step, array $result): void
    {
        $isTemplate = $step->action_type === FlowStep::ACTION_SEND_TEMPLATE;

        Message::create([
            'conversation_id' => $conversation->id,
            'direction' => Message::OUT,
            'type' => $isTemplate ? 'template' : 'text',
            'body' => $isTemplate ? 'Automated reply' : (string) ($step->payload['body'] ?? ''),
            'meta_message_id' => $result['message_id'] ?? null,
            'status' => $result['ok'] ? Message::STATUS_SENT : Message::STATUS_FAILED,
            'error' => $result['ok'] ? null : $result['error'],
            'sent_at' => now(),
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();
    }
}
