<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Scopes\TenantScope;
use App\Services\WhatsApp\CampaignDispatcher;
use App\Tenancy\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Sends a campaign off the request cycle, inside its owning tenant's context.
 *
 * Carries the campaign id rather than the model so it always works from the
 * current row and never a stale serialised copy.
 */
class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public int $campaignId)
    {
    }

    public function handle(TenantManager $tenants, CampaignDispatcher $dispatcher): void
    {
        $campaign = Campaign::withoutGlobalScope(TenantScope::class)->find($this->campaignId);

        if (!$campaign) {
            Log::warning('SendCampaign for a deleted campaign', ['campaign_id' => $this->campaignId]);

            return;
        }

        $tenant = $campaign->tenant;

        if (!$tenant) {
            Log::warning('SendCampaign for a campaign with no tenant', ['campaign_id' => $campaign->id]);

            return;
        }

        $tenants->runForTenant($tenant, fn () => $dispatcher->send($campaign));
    }
}
