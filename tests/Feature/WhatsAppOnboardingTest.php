<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Manual onboarding: a tenant admin pastes credentials, we prove them against
 * Meta before storing them as connected, and the resulting row is visible only
 * to that tenant.
 */
class WhatsAppOnboardingTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsTenantAdmin(Tenant $tenant): User
    {
        $user = User::factory()->forTenant($tenant)->tenantAdmin()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_a_tenant_can_connect_a_number_meta_accepts(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response([
            'id' => '123456789012345',
            'display_phone_number' => '+91 90000 00000',
            'verified_name' => 'Demo Clinic',
        ], 200)]);

        $tenant = Tenant::factory()->create();
        $this->actingAsTenantAdmin($tenant);

        $this->post(route('tenant.whatsapp.store'), [
            'label' => 'Main line',
            'phone_number_id' => '123456789012345',
            'access_token' => 'EAAtoken',
            'app_secret' => 'secret',
        ])->assertRedirect(route('tenant.whatsapp.index'));

        $account = WhatsappAccount::withoutGlobalScopes()->first();

        $this->assertNotNull($account);
        $this->assertSame($tenant->id, $account->tenant_id);
        $this->assertTrue($account->isConnected());
        $this->assertSame('+91 90000 00000', $account->display_phone_number);
        $this->assertNotEmpty($account->verify_token, 'a per-account verify token is generated');
        $this->assertTrue($account->is_default, 'the first number becomes the default');
    }

    public function test_credentials_meta_rejects_are_not_stored(): void
    {
        Http::fake(['graph.facebook.com/*' => Http::response([
            'error' => ['message' => 'Invalid OAuth access token.'],
        ], 401)]);

        $tenant = Tenant::factory()->create();
        $this->actingAsTenantAdmin($tenant);

        $this->post(route('tenant.whatsapp.store'), [
            'phone_number_id' => '123456789012345',
            'access_token' => 'wrong',
        ])->assertSessionHasErrors('access_token');

        $this->assertSame(0, WhatsappAccount::withoutGlobalScopes()->count());
    }

    public function test_a_tenant_cannot_act_on_another_tenants_account(): void
    {
        $mine = Tenant::factory()->create();
        $theirs = Tenant::factory()->create();

        $foreign = WhatsappAccount::factory()->forTenant($theirs)->create();

        $this->actingAsTenantAdmin($mine);

        $this->delete(route('tenant.whatsapp.destroy', $foreign))->assertNotFound();

        $this->assertNotNull(WhatsappAccount::withoutGlobalScopes()->find($foreign->id));
    }

    public function test_the_connection_list_shows_only_your_own_numbers(): void
    {
        $mine = Tenant::factory()->create();
        $theirs = Tenant::factory()->create();

        $ours = WhatsappAccount::factory()->forTenant($mine)->create();
        $foreign = WhatsappAccount::factory()->forTenant($theirs)->create();

        $this->actingAsTenantAdmin($mine);

        $this->get(route('tenant.whatsapp.index'))
            ->assertOk()
            ->assertSee($ours->phone_number_id)
            ->assertDontSee($foreign->phone_number_id);
    }

    public function test_meta_handshake_accepts_a_per_account_verify_token(): void
    {
        $account = WhatsappAccount::factory()->create(['webhook_status' => 'unverified']);

        $this->get('/api/webhook?hub_mode=subscribe&hub_verify_token='.$account->verify_token.'&hub_challenge=CHALLENGE42')
            ->assertOk()
            ->assertSee('CHALLENGE42');

        $this->assertSame('verified', $account->fresh()->webhook_status);
    }

    public function test_meta_handshake_rejects_an_unknown_verify_token(): void
    {
        WhatsappAccount::factory()->create();

        $this->get('/api/webhook?hub_mode=subscribe&hub_verify_token=not-a-real-token&hub_challenge=CHALLENGE42')
            ->assertForbidden();
    }
}
