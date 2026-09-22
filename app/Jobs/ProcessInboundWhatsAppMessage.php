<?php

namespace App\Jobs;

use App\Models\Scopes\TenantScope;
use App\Models\WhatsappAccount;
use App\Services\WhatsApp\InboundMessageHandler;
use App\Tenancy\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Handles one inbound webhook payload inside its owning tenant's context, off
 * the request cycle so the endpoint can answer Meta immediately.
 *
 * Carries the account id rather than the model so encrypted credentials are
 * never serialised into the queue payload.
 */
class ProcessInboundWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public int $whatsappAccountId,
        public array $payload,
    ) {
    }

    public function handle(TenantManager $tenants, InboundMessageHandler $handler): void
    {
        $account = WhatsappAccount::withoutGlobalScope(TenantScope::class)->find($this->whatsappAccountId);

        if (!$account) {
            Log::warning('Inbound WhatsApp payload for a deleted account', [
                'whatsapp_account_id' => $this->whatsappAccountId,
            ]);

            return;
        }

        $tenant = $account->tenant;

        if (!$tenant) {
            Log::warning('Inbound WhatsApp payload for an account with no tenant', [
                'whatsapp_account_id' => $account->id,
            ]);

            return;
        }

        $tenants->runForTenant($tenant, fn () => $handler->handle($account, $this->payload));
    }
}
