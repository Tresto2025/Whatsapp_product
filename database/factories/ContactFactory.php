<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'wa_id' => (string) fake()->unique()->numerify('9198#######'),
            'profile_name' => fake()->name(),
            'last_inbound_at' => now(),
        ];
    }

    public function forTenant(Tenant|int $tenant): static
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn () => ['tenant_id' => $id]);
    }

    public function optedOut(): static
    {
        return $this->state(fn () => ['opted_out_at' => now()]);
    }
}
