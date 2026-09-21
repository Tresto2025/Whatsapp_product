<?php

namespace Database\Seeders;

use App\Models\User;
use App\Tenancy\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Ensures a platform super admin exists. Tenants are created by signing up, so
 * nothing else is seeded by default.
 */
class TenancySeeder extends Seeder
{
    public function run(): void
    {
        app(TenantManager::class)->runWithoutScope(function () {
            if (User::where('role', User::ROLE_SUPER_ADMIN)->exists()) {
                return;
            }

            $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');

            User::create([
                'tenant_id' => null,
                'name' => 'Platform Admin',
                'email' => $email,
                'role' => User::ROLE_SUPER_ADMIN,
                'is_active' => true,
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'ChangeMe!123')),
            ]);

            $this->command?->warn("Created super admin [{$email}]. Change the password immediately.");
        });
    }
}
