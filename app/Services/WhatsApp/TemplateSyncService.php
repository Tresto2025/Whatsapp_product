<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use RuntimeException;

/**
 * Pulls a tenant's approved templates from Meta into whatsapp_templates.
 *
 * Templates live on Meta; we only mirror them, so this is a one-way sync —
 * paging through GET /{waba_id}/message_templates and upserting each one keyed
 * by (account, name, language), which is what Meta treats as unique.
 */
class TemplateSyncService
{
    /**
     * @return array{synced:int}
     * @throws RuntimeException when the account cannot be queried.
     */
    public function sync(WhatsappAccount $account): array
    {
        if (empty($account->waba_id)) {
            throw new RuntimeException('This number has no WhatsApp Business Account ID, so its templates cannot be synced.');
        }

        $client = WhatsAppClient::forAccount($account);
        $after = null;
        $synced = 0;

        do {
            $result = $client->fetchTemplates($account->waba_id, 100, $after);

            if (!$result['ok']) {
                throw new RuntimeException('Meta rejected the template request: '.($result['error'] ?? 'unknown error'));
            }

            foreach ($result['json']['data'] ?? [] as $remote) {
                $this->store($account, $remote);
                $synced++;
            }

            $after = $result['json']['paging']['cursors']['after'] ?? null;
            $hasNext = isset($result['json']['paging']['next']) && $after;
        } while ($hasNext);

        return ['synced' => $synced];
    }

    private function store(WhatsappAccount $account, array $remote): void
    {
        $parsed = $this->parseComponents($remote['components'] ?? []);

        WhatsappTemplate::updateOrCreate(
            [
                'whatsapp_account_id' => $account->id,
                'name' => $remote['name'] ?? '',
                'language' => $remote['language'] ?? 'en',
            ],
            [
                'meta_template_id' => $remote['id'] ?? null,
                'category' => isset($remote['category']) ? strtolower($remote['category']) : null,
                'status' => isset($remote['status']) ? strtolower($remote['status']) : WhatsappTemplate::STATUS_PENDING,
                'header_type' => $parsed['header_type'],
                'body' => $parsed['body'],
                'footer' => $parsed['footer'],
                'buttons' => $parsed['buttons'],
                'variables' => $parsed['variables'],
                'rejection_reason' => $remote['rejected_reason'] ?? null,
                'synced_at' => now(),
            ],
        );
    }

    /**
     * Flatten Meta's component array into the columns we store.
     *
     * @return array{header_type:?string,body:?string,footer:?string,buttons:?array,variables:array}
     */
    private function parseComponents(array $components): array
    {
        $out = ['header_type' => null, 'body' => null, 'footer' => null, 'buttons' => null, 'variables' => []];

        foreach ($components as $component) {
            switch (strtoupper($component['type'] ?? '')) {
                case 'HEADER':
                    $out['header_type'] = strtolower($component['format'] ?? 'text');
                    break;
                case 'BODY':
                    $out['body'] = $component['text'] ?? null;
                    $out['variables'] = $this->placeholders($component['text'] ?? '');
                    break;
                case 'FOOTER':
                    $out['footer'] = $component['text'] ?? null;
                    break;
                case 'BUTTONS':
                    $out['buttons'] = $component['buttons'] ?? null;
                    break;
            }
        }

        return $out;
    }

    /** The distinct positional placeholders {{1}}, {{2}}… a body expects, in order. */
    private function placeholders(string $text): array
    {
        preg_match_all('/\{\{\s*(\d+)\s*\}\}/', $text, $matches);

        return array_values(array_unique($matches[1]));
    }
}
