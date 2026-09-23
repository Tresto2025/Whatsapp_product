<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'name' => fake()->unique()->word(),
        ];
    }

    public function forTenant(Tenant|int $tenant): static
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn () => ['tenant_id' => $id]);
    }
}
