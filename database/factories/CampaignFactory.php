<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\Tenant;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();
        $account = WhatsappAccount::factory()->forTenant($tenant)->create();
        $template = WhatsappTemplate::factory()->forAccount($account)->create();

        return [
            'tenant_id' => $tenant->id,
            'whatsapp_account_id' => $account->id,
            'template_id' => $template->id,
            'name' => fake()->words(2, true).' campaign',
            'segment' => ['type' => 'all', 'tags' => []],
            'status' => Campaign::STATUS_DRAFT,
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(function () use ($tenant) {
            $account = WhatsappAccount::factory()->forTenant($tenant)->create();
            $template = WhatsappTemplate::factory()->forAccount($account)->create();

            return [
                'tenant_id' => $tenant->id,
                'whatsapp_account_id' => $account->id,
                'template_id' => $template->id,
            ];
        });
    }
}
