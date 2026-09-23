<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatsappTemplate>
 */
class WhatsappTemplateFactory extends Factory
{
    protected $model = WhatsappTemplate::class;

    public function definition(): array
    {
        $tenant = Tenant::factory()->create();

        return [
            'tenant_id' => $tenant->id,
            'whatsapp_account_id' => WhatsappAccount::factory()->forTenant($tenant),
            'meta_template_id' => (string) fake()->numerify('##############'),
            'name' => fake()->unique()->slug(2, false).'_reminder',
            'language' => 'en',
            'category' => WhatsappTemplate::CATEGORY_MARKETING,
            'status' => WhatsappTemplate::STATUS_APPROVED,
            'header_type' => 'text',
            'body' => 'Hi {{1}}, your booking on {{2}} is confirmed.',
            'variables' => ['1', '2'],
            'synced_at' => now(),
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn () => [
            'tenant_id' => $tenant->id,
            'whatsapp_account_id' => WhatsappAccount::factory()->forTenant($tenant),
        ]);
    }

    public function forAccount(WhatsappAccount $account): static
    {
        return $this->state(fn () => [
            'tenant_id' => $account->tenant_id,
            'whatsapp_account_id' => $account->id,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => WhatsappTemplate::STATUS_APPROVED]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => WhatsappTemplate::STATUS_PENDING]);
    }
}
