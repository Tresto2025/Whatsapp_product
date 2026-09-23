<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Models\WhatsappTemplate;
use App\Tenancy\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * A ready-to-explore demo workspace for local runs: one tenant admin, a
 * connected number, an approved template, and a handful of contacts with
 * inbound conversations. Idempotent, so it is safe to re-run.
 *
 * Not part of the default seed — run explicitly with:
 *   php artisan db:seed --class=Database\\Seeders\\DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'demo'],
            ['name' => 'Demo Cafe', 'status' => 'active'],
        );

        $admin = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'tenant_id' => $tenant->id,
                'name' => 'Demo Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TENANT_ADMIN,
                'is_active' => true,
            ],
        );

        $tenant->forceFill(['owner_user_id' => $admin->id])->save();

        app(TenantManager::class)->runForTenant($tenant, function () use ($tenant) {
            $account = WhatsappAccount::firstOrCreate(
                ['phone_number_id' => 'demo-100200300'],
                [
                    'tenant_id' => $tenant->id,
                    'label' => 'Main line',
                    'waba_id' => 'demo-waba',
                    'display_phone_number' => '+91 90000 00000',
                    'access_token' => 'EAAdemotoken',
                    'app_secret' => 'demosecret',
                    'verify_token' => 'demo-verify',
                    'provider' => 'manual',
                    'connection_status' => WhatsappAccount::STATUS_CONNECTED,
                    'webhook_status' => 'verified',
                    'is_default' => true,
                    'last_verified_at' => now(),
                ],
            );

            WhatsappTemplate::firstOrCreate(
                ['whatsapp_account_id' => $account->id, 'name' => 'table_confirmation', 'language' => 'en'],
                [
                    'tenant_id' => $tenant->id,
                    'category' => WhatsappTemplate::CATEGORY_UTILITY,
                    'status' => WhatsappTemplate::STATUS_APPROVED,
                    'header_type' => 'text',
                    'body' => 'Hi {{1}}, your table on {{2}} is confirmed.',
                    'variables' => ['1', '2'],
                    'synced_at' => now(),
                ],
            );

            foreach (['Aarav', 'Priya', 'Rahul', 'Sara', 'Vikram'] as $i => $name) {
                $contact = Contact::firstOrCreate(
                    ['wa_id' => '91980000000'.$i],
                    ['tenant_id' => $tenant->id, 'profile_name' => $name, 'last_inbound_at' => now()->subMinutes($i * 7)],
                );

                $conversation = Conversation::firstOrCreate(
                    ['contact_id' => $contact->id, 'whatsapp_account_id' => $account->id],
                    [
                        'tenant_id' => $tenant->id,
                        'status' => Conversation::STATUS_OPEN,
                        'last_message_at' => now()->subMinutes($i * 7),
                        'unread_count' => $i % 2,
                    ],
                );

                Message::firstOrCreate(
                    ['meta_message_id' => 'seed-in-'.$i],
                    [
                        'tenant_id' => $tenant->id,
                        'conversation_id' => $conversation->id,
                        'direction' => Message::IN,
                        'type' => 'text',
                        'body' => 'Do you have a table for '.($i + 2).'?',
                        'status' => Message::STATUS_DELIVERED,
                        'sent_at' => now()->subMinutes($i * 7),
                    ],
                );
            }
        });

        $this->command?->info('Demo workspace ready — sign in as demo@example.com / password');
    }
}
