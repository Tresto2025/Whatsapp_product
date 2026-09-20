<?php

namespace App\Jobs;

use App\Http\Controllers\WebhookController;
use App\Models\Scopes\TenantScope;
use App\Models\WhatsappAccount;
use App\Tenancy\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Handles one inbound WhatsApp webhook payload inside its owning tenant's
 * context, off the request cycle so the webhook endpoint can answer Meta fast.
 *
 * The account id (not the model) is carried so the encrypted credentials are
 * never serialised into the queue payload.
 *
 * NOTE: with QUEUE_CONNECTION=sync this still runs inline — correct, just not
 * fast. Set the queue to `database` and run a worker to get the latency win.
 */
class ProcessInboundWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public ?int $whatsappAccountId,
        public array $payload,
    ) {
    }

    public function handle(TenantManager $tenants): void
    {
        $account = $this->whatsappAccountId
            ? WhatsappAccount::withoutGlobalScope(TenantScope::class)->find($this->whatsappAccountId)
            : null;

        $handle = fn () => app(WebhookController::class)->handleInbound($this->payload);

        // Legacy single-tenant path: no connected account, so run unscoped
        // exactly as before tenancy existed.
        if (!$account) {
            $handle();
            return;
        }

        $tenant = $account->tenant;

        if (!$tenant) {
            Log::warning('Inbound WhatsApp message for an account with no tenant', [
                'whatsapp_account_id' => $account->id,
            ]);
            return;
        }

        $tenants->runForTenant($tenant, $handle);
    }
}
