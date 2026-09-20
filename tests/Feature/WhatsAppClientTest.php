<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\WhatsappAccount;
use App\Services\WhatsApp\WhatsAppClient;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The client is the guarantee that a tenant can only ever send from a number it
 * owns: it is always built from a whatsapp_accounts row, never from a shared
 * platform credential.
 */
class WhatsAppClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_from_the_tenants_own_number_and_token(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.OK']]], 200)]);

        $account = WhatsappAccount::factory()->create();

        $result = WhatsAppClient::forAccount($account)->sendText('919111111111', 'hello');

        $this->assertTrue($result['ok']);
        $this->assertSame('wamid.OK', $result['message_id']);

        Http::assertSent(function ($request) use ($account) {
            return str_contains($request->url(), $account->phone_number_id.'/messages')
                && $request->hasHeader('Authorization', 'Bearer '.$account->access_token)
                && $request['text']['body'] === 'hello';
        });
    }

    public function test_current_resolves_the_active_tenants_account(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'x']]], 200)]);

        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        $accountA = WhatsappAccount::factory()->forTenant($tenantA)->create();
        $accountB = WhatsappAccount::factory()->forTenant($tenantB)->create();

        app(TenantManager::class)->set($tenantB);

        $client = WhatsAppClient::current();

        $this->assertNotNull($client);
        $this->assertSame($accountB->phone_number_id, $client->phoneNumberId());
        $this->assertNotSame($accountA->phone_number_id, $client->phoneNumberId());
    }

    public function test_a_tenant_without_a_connected_account_does_not_borrow_another_tenants(): void
    {
        $tenantWithNumber = Tenant::factory()->create();
        WhatsappAccount::factory()->forTenant($tenantWithNumber)->create();

        $tenantWithout = Tenant::factory()->create();

        $this->assertNull(WhatsAppClient::forTenant($tenantWithout));
    }

    public function test_a_pending_account_is_not_used_for_sending(): void
    {
        $tenant = Tenant::factory()->create();
        WhatsappAccount::factory()->forTenant($tenant)->pending()->create();

        $this->assertNull(WhatsAppClient::forTenant($tenant));
    }

    public function test_the_default_number_is_preferred(): void
    {
        $tenant = Tenant::factory()->create();

        WhatsappAccount::factory()->forTenant($tenant)->create(['is_default' => false]);
        $default = WhatsappAccount::factory()->forTenant($tenant)->create(['is_default' => true]);

        $this->assertSame($default->phone_number_id, WhatsAppClient::forTenant($tenant)->phoneNumberId());
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $account = WhatsappAccount::factory()->create(['access_token' => 'super-secret-token']);

        $stored = DB::table('whatsapp_accounts')->where('id', $account->id)->value('access_token');

        $this->assertNotSame('super-secret-token', $stored, 'the token must not be stored in plaintext');
        $this->assertStringNotContainsString('super-secret-token', (string) $stored);
        $this->assertSame('super-secret-token', $account->fresh()->access_token, 'and must decrypt back');
    }

    public function test_credentials_are_hidden_from_serialisation(): void
    {
        $account = WhatsappAccount::factory()->create();

        $array = $account->toArray();

        $this->assertArrayNotHasKey('access_token', $array);
        $this->assertArrayNotHasKey('app_secret', $array);
    }

    public function test_accounts_are_isolated_between_tenants(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();

        WhatsappAccount::factory()->forTenant($tenantA)->create();
        $foreign = WhatsappAccount::factory()->forTenant($tenantB)->create();

        app(TenantManager::class)->set($tenantA);

        $this->assertNull(WhatsappAccount::find($foreign->id));
        $this->assertSame(1, WhatsappAccount::count());
    }
}
