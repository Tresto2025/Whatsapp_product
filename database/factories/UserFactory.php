<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * The users table splits the name into first_name/last_name and carries a
     * role column, so this does not use the Laravel skeleton's single `name`.
     * Every non-super-admin user belongs to a tenant, which ResolveTenant
     * enforces on web requests, so one is created by default.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_TENANT_STAFF,
            'status' => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Attach the user to an existing tenant.
     */
    public function forTenant(int|Tenant $tenant): static
    {
        $id = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return $this->state(fn (array $attributes) => ['tenant_id' => $id]);
    }

    /**
     * A tenant admin rather than staff.
     */
    public function tenantAdmin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_TENANT_ADMIN]);
    }

    /**
     * A platform super admin, which belongs to no tenant.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_SUPER_ADMIN,
            'tenant_id' => null,
        ]);
    }
}
