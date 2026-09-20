<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Tenancy\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Two demo tenants, each with an admin and its own connected WhatsApp number,
 * so multi-tenancy can actually be exercised locally.
 *
 * Opt-in: php artisan db:seed --class=DemoSeeder
 * Never run this in production — the passwords are fixed and the WhatsApp
 * credentials are fake (they route correctly but cannot send).
 */
class DemoSeeder extends Seeder
{
    private const DEMOS = [
        ['name' => 'Northside Clinic', 'slug' => 'northside', 'pnid' => '100000000000001', 'number' => '+91 90000 00001'],
        ['name' => 'Harbour Dental',   'slug' => 'harbour',   'pnid' => '100000000000002', 'number' => '+91 90000 00002'],
    ];

    public function run(): void
    {
        app(TenantManager::class)->runWithoutScope(function () {
            foreach (self::DEMOS as $demo) {
                $tenant = Tenant::firstOrCreate(
                    ['slug' => $demo['slug']],
                    ['name' => $demo['name'], 'status' => 'active']
                );

                $email = $demo['slug'].'@example.com';

                $admin = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'tenant_id' => $tenant->id,
                        'first_name' => explode(' ', $demo['name'])[0],
                        'last_name' => 'Admin',
                        'role' => User::ROLE_TENANT_ADMIN,
                        'status' => 1,
                        'password' => Hash::make('password'),
                    ]
                );

                $tenant->forceFill(['owner_user_id' => $admin->id])->save();

                WhatsappAccount::withoutGlobalScopes()->updateOrCreate(
                    ['phone_number_id' => $demo['pnid']],
                    [
                        'tenant_id' => $tenant->id,
                        'label' => $demo['name'].' main line',
                        'display_phone_number' => $demo['number'],
                        'access_token' => 'DEMO-TOKEN-'.strtoupper($demo['slug']),
                        'app_secret' => 'demo-secret-'.$demo['slug'],
                        'verify_token' => 'demo-verify-'.$demo['slug'],
                        'provider' => 'manual',
                        'connection_status' => WhatsappAccount::STATUS_CONNECTED,
                        'webhook_status' => 'verified',
                        'is_default' => true,
                        'last_verified_at' => now(),
                    ]
                );

                $this->command?->info("Tenant [{$demo['name']}] — login {$email} / password — number {$demo['pnid']}");
            }
        });
    }
}
