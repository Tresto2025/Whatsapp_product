<?php

namespace App\Services\WhatsApp;

use App\Models\Tenant;
use App\Models\WhatsappAccount;
use App\Models\Scopes\TenantScope;
use App\Tenancy\TenantManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Talks to the Meta WhatsApp Cloud API on behalf of ONE account.
 *
 * This replaces the per-controller credential reads that used to pull a single
 * platform-wide token out of config. A client is always constructed from a
 * concrete phone_number_id + token pair, so a tenant can only ever send from a
 * number it owns.
 *
 * Resolution order used by current():
 *   1. the current tenant's connected account (multi-tenant path), else
 *   2. the platform credentials in config (legacy single-tenant fallback, so
 *      existing flows keep working as "Tenant #1" until they are connected).
 */
class WhatsAppClient
{
    public function __construct(
        private string $phoneNumberId,
        private string $token,
        private string $apiVersion = 'v22.0',
        private ?WhatsappAccount $account = null,
    ) {
    }

    public static function forAccount(WhatsappAccount $account): self
    {
        return new self(
            $account->phone_number_id,
            (string) $account->access_token,
            config('services.whatsapp.api_version', 'v22.0'),
            $account,
        );
    }

    /**
     * The account a tenant sends from: its default connected number, or the
     * first connected one. Null when the tenant has not connected a number.
     */
    public static function accountForTenant(Tenant|int|null $tenant): ?WhatsappAccount
    {
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        if (!$tenantId) {
            return null;
        }

        return WhatsappAccount::withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenantId)
            ->where('connection_status', WhatsappAccount::STATUS_CONNECTED)
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }

    public static function forTenant(Tenant|int|null $tenant): ?self
    {
        $account = self::accountForTenant($tenant);

        return $account ? self::forAccount($account) : null;
    }

    /**
     * Platform-level credentials from config. Kept as the fallback for flows
     * that have not been migrated to a tenant-owned number yet.
     */
    public static function fromConfig(): ?self
    {
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $token = config('services.whatsapp.token');

        if (empty($phoneNumberId) || empty($token)) {
            return null;
        }

        return new self(
            (string) $phoneNumberId,
            (string) $token,
            config('services.whatsapp.api_version', 'v22.0'),
        );
    }

    /**
     * The client for whoever the request currently belongs to.
     */
    public static function current(): ?self
    {
        $tenantId = app(TenantManager::class)->id();

        return self::forTenant($tenantId) ?? self::fromConfig();
    }

    public function account(): ?WhatsappAccount
    {
        return $this->account;
    }

    public function phoneNumberId(): string
    {
        return $this->phoneNumberId;
    }

    private function endpoint(string $path): string
    {
        return "https://graph.facebook.com/{$this->apiVersion}/{$path}";
    }

    /**
     * POST a raw message payload. Returns a normalised result rather than the
     * raw body string, so callers can branch on success without re-parsing.
     *
     * @return array{ok:bool,status:int,json:array,raw:string,error:?string,message_id:?string}
     */
    public function send(array $payload): array
    {
        $payload['messaging_product'] ??= 'whatsapp';

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($this->endpoint("{$this->phoneNumberId}/messages"), $payload);
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', [
                'phone_number_id' => $this->phoneNumberId,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'status' => 0,
                'json' => [],
                'raw' => '',
                'error' => $e->getMessage(),
                'message_id' => null,
            ];
        }

        $json = $response->json() ?? [];
        $ok = $response->successful();

        if (!$ok) {
            Log::warning('WhatsApp API error', [
                'phone_number_id' => $this->phoneNumberId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }

        return [
            'ok' => $ok,
            'status' => $response->status(),
            'json' => $json,
            'raw' => $response->body(),
            'error' => $ok ? null : ($json['error']['message'] ?? $response->body()),
            'message_id' => $json['messages'][0]['id'] ?? null,
        ];
    }

    public function sendText(string $to, string $body): array
    {
        return $this->send([
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $body],
        ]);
    }

    /**
     * Send an approved template. $bodyParams are positional {{1}}, {{2}}… text
     * values; $headerImageUrl adds an image header component when given.
     */
    public function sendTemplate(
        string $to,
        string $templateName,
        array $bodyParams = [],
        string $languageCode = 'en',
        ?string $headerImageUrl = null,
    ): array {
        $components = [];

        if ($headerImageUrl) {
            $components[] = [
                'type' => 'header',
                'parameters' => [
                    ['type' => 'image', 'image' => ['link' => $headerImageUrl]],
                ],
            ];
        }

        if ($bodyParams !== []) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(
                    fn ($value) => ['type' => 'text', 'text' => (string) $value],
                    array_values($bodyParams),
                ),
            ];
        }

        $template = [
            'name' => $templateName,
            'language' => ['code' => $languageCode],
        ];

        if ($components !== []) {
            $template['components'] = $components;
        }

        return $this->send([
            'to' => $to,
            'type' => 'template',
            'template' => $template,
        ]);
    }

    /**
     * GET /{phone_number_id} — used to prove a pasted token actually controls
     * the number before the credentials are marked connected.
     *
     * @return array{ok:bool,status:int,json:array,error:?string}
     */
    public function verifyCredentials(): array
    {
        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->timeout(20)
                ->get($this->endpoint($this->phoneNumberId), [
                    'fields' => 'id,display_phone_number,verified_name,quality_rating',
                ]);
        } catch (\Throwable $e) {
            return ['ok' => false, 'status' => 0, 'json' => [], 'error' => $e->getMessage()];
        }

        $json = $response->json() ?? [];

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'json' => $json,
            'error' => $response->successful() ? null : ($json['error']['message'] ?? $response->body()),
        ];
    }
}
