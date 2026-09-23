<?php

namespace App\Services\WhatsApp;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Str;

/**
 * Sends one campaign's pending recipients through the tenant's WhatsApp number.
 *
 * Each send becomes a Message row (so it lands in the contact's transcript and
 * picks up delivery receipts later) and updates that recipient's per-person
 * status. Marketing templates skip opted-out contacts; utility templates follow
 * Meta's own rules. Assumes it runs inside the campaign's tenant context.
 */
class CampaignDispatcher
{
    public function send(Campaign $campaign): void
    {
        $campaign->loadMissing('account', 'template');
        $account = $campaign->account;
        $template = $campaign->template;

        if (!$account || !$account->isUsable() || !$template) {
            $campaign->forceFill(['status' => Campaign::STATUS_FAILED])->save();

            return;
        }

        $campaign->forceFill([
            'status' => Campaign::STATUS_SENDING,
            'started_at' => $campaign->started_at ?? now(),
        ])->save();

        $client = WhatsAppClient::forAccount($account);
        $isMarketing = $template->category === WhatsappTemplate::CATEGORY_MARKETING;
        $throttleMs = (int) config('services.whatsapp.campaign_throttle_ms', 0);

        $counts = ['total' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0];

        $recipients = $campaign->recipients()
            ->where('status', CampaignRecipient::STATUS_PENDING)
            ->with('contact')
            ->get();

        foreach ($recipients as $recipient) {
            $counts['total']++;
            $contact = $recipient->contact;

            if (!$contact) {
                $recipient->forceFill(['status' => CampaignRecipient::STATUS_SKIPPED, 'failed_reason' => 'contact removed'])->save();
                $counts['skipped']++;
                continue;
            }

            if ($isMarketing && $contact->hasOptedOut()) {
                $recipient->forceFill(['status' => CampaignRecipient::STATUS_SKIPPED, 'failed_reason' => 'opted out'])->save();
                $counts['skipped']++;
                continue;
            }

            $params = $this->resolveParams($template, $campaign->variable_map ?? [], $contact);
            $result = $client->sendTemplate($contact->wa_id, $template->name, $params, $template->language);

            $conversation = Conversation::firstOrCreate(
                ['contact_id' => $contact->id, 'whatsapp_account_id' => $account->id],
                ['status' => Conversation::STATUS_OPEN],
            );

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'direction' => Message::OUT,
                'type' => 'template',
                'body' => $this->renderPreview($template, $params),
                'template_id' => $template->id,
                'meta_message_id' => $result['message_id'] ?? null,
                'status' => $result['ok'] ? Message::STATUS_SENT : Message::STATUS_FAILED,
                'error' => $result['ok'] ? null : $result['error'],
                'sent_at' => now(),
            ]);

            $recipient->forceFill([
                'message_id' => $message->id,
                'status' => $result['ok'] ? CampaignRecipient::STATUS_SENT : CampaignRecipient::STATUS_FAILED,
                'failed_reason' => $result['ok'] ? null : $result['error'],
            ])->save();

            if ($result['ok']) {
                $counts['sent']++;
                $conversation->forceFill(['last_message_at' => now()])->save();
                $contact->forceFill(['last_outbound_at' => now()])->save();
            } else {
                $counts['failed']++;
            }

            if ($throttleMs > 0) {
                usleep($throttleMs * 1000);
            }
        }

        $campaign->forceFill([
            'status' => Campaign::STATUS_SENT,
            'completed_at' => now(),
            'counts' => $counts,
        ])->save();
    }

    /**
     * The positional body parameters for one contact, in template order.
     *
     * @return list<string>
     */
    private function resolveParams(WhatsappTemplate $template, array $variableMap, Contact $contact): array
    {
        $params = [];

        foreach ($template->variables ?? [] as $position) {
            $params[] = $this->resolveOne($variableMap[$position] ?? null, $contact);
        }

        return $params;
    }

    private function resolveOne(?array $map, Contact $contact): string
    {
        if (!$map) {
            return '';
        }

        if (($map['source'] ?? null) === 'static') {
            return (string) ($map['value'] ?? '');
        }

        if (($map['source'] ?? null) === 'field') {
            return match ($map['field'] ?? '') {
                'name' => $contact->displayName(),
                'profile_name' => (string) $contact->profile_name,
                'wa_id' => (string) $contact->wa_id,
                'email' => (string) $contact->email,
                default => '',
            };
        }

        return '';
    }

    private function renderPreview(WhatsappTemplate $template, array $params): string
    {
        $body = (string) $template->body;

        foreach ($template->variables ?? [] as $index => $position) {
            $body = str_replace('{{'.$position.'}}', $params[$index] ?? '', $body);
        }

        return Str::of($body)->trim()->value() ?: $template->name;
    }
}
