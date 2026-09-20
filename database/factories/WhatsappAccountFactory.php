<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\WhatsappAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WhatsappAccount>
 */
class WhatsappAccountFactory extends Factory
{
    protected $model = WhatsappAccount::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'label' => fake()->words(2, true),
            'phone_number_id' => (string) fake()->unique()->numerify('###############'),
            'waba_id' => (string) fake()->numerify('###############'),
            'display_phone_number' => fake()->numerify('+91 98### #####'),
            'access_token' => 'EAA'.Str::random(40),
            'app_secret' => Str::random(32),
            'verify_token' => WhatsappAccount::generateVerifyToken(),
            'provider' => 'manual',
            'connection_status' => WhatsappAccount::STATUS_CONNECTED,
            'webhook_status' => 'verified',
            'is_default' => true,
            'last_verified_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'connection_status' => WhatsappAccount::STATUS_PENDING,
            'webhook_status' => 'unverified',
            'last_verified_at' => null,
        ]);
    }

    public function withoutAppSecret(): static
    {
        return $this->state(fn () => ['app_secret' => null]);
    }

    public function forTenant(Tenant|int $tenant): static
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn () => ['tenant_id' => $id]);
    }
}
