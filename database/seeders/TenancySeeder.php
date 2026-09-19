<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Bootstraps tenancy:
 *  - ensures a default tenant exists (matches the migration backfill),
 *  - ensures a platform super-admin account exists (role = 0).
 *
 * Credentials are taken from env so no secret is committed:
 *   SUPER_ADMIN_EMAIL, SUPER_ADMIN_PASSWORD (defaults provided for local dev).
 */
class TenancySeeder extends Seeder
{
    public function run(): void
    {
        // The super admin is global, so make sure no tenant scope is applied.
        app(TenantManager::class)->runWithoutScope(function () {
            $tenant = Tenant::firstOrCreate(
                ['slug' => 'default'],
                ['name' => 'Default Tenant', 'status' => 'active']
            );

            $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');

            $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();

            if (!$superAdmin) {
                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => 'Super',
                        'last_name' => 'Admin',
                        'role' => User::ROLE_SUPER_ADMIN,
                        'status' => 1,
                        'tenant_id' => null, // platform-level, not tied to a tenant
                        'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'ChangeMe!123')),
                    ]
                );

                $this->command?->warn(
                    "Created super admin [{$email}]. Change the password immediately."
                );
            }
        });
    }
}
