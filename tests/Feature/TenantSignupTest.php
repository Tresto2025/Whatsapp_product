<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A business signing itself up must end up with a workspace it can actually
 * use. Before this existed, registration produced a user with no tenant_id,
 * which ResolveTenant refuses — so the signup succeeded and the account was
 * locked out of every page.
 */
class TenantSignupTest extends TestCase
{
    use RefreshDatabase;

    private array $valid = [
        'business_name' => 'Riverside Physio',
        'name' => 'Sam Patel',
        'email' => 'sam@riverside.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    public function test_signing_up_creates_a_workspace_owned_by_the_registrant(): void
    {
        $this->post(route('tenant.signup.store'), $this->valid)
            ->assertRedirect(route('tenant.whatsapp.create'));

        $tenant = Tenant::where('slug', 'riverside-physio')->first();
        $user = User::where('email', 'sam@riverside.test')->first();

        $this->assertNotNull($tenant);
        $this->assertSame('Riverside Physio', $tenant->name);
        $this->assertSame($user->id, $tenant->owner_user_id);

        $this->assertSame($tenant->id, $user->tenant_id);
        $this->assertTrue($user->isTenantAdmin(), 'the registrant administers their own workspace');
        $this->assertAuthenticatedAs($user);
    }

    public function test_the_new_tenant_can_reach_its_dashboard_and_connect_a_number(): void
    {
        $this->post(route('tenant.signup.store'), $this->valid);

        $this->get('/dashboard')->assertOk();
        $this->get(route('tenant.whatsapp.index'))->assertOk();
        $this->get(route('tenant.whatsapp.create'))->assertOk();
    }

    public function test_a_new_tenant_sees_no_other_tenants_numbers(): void
    {
        $other = Tenant::factory()->create();
        $foreign = WhatsappAccount::factory()->forTenant($other)->create();

        $this->post(route('tenant.signup.store'), $this->valid);

        $this->get(route('tenant.whatsapp.index'))
            ->assertOk()
            ->assertDontSee($foreign->phone_number_id);
    }

    public function test_two_businesses_with_the_same_name_get_distinct_slugs(): void
    {
        $this->post(route('tenant.signup.store'), $this->valid);
        $this->post('/logout');

        $second = array_merge($this->valid, ['email' => 'other@riverside.test']);
        $this->post(route('tenant.signup.store'), $second);

        $slugs = Tenant::where('name', 'Riverside Physio')->pluck('slug')->all();

        $this->assertCount(2, $slugs);
        $this->assertSame(count($slugs), count(array_unique($slugs)), 'slugs must stay unique');
    }

    public function test_a_duplicate_email_is_rejected_without_creating_a_workspace(): void
    {
        User::factory()->forTenant(Tenant::factory()->create())->create(['email' => 'sam@riverside.test']);

        $before = Tenant::count();

        $this->post(route('tenant.signup.store'), $this->valid)->assertSessionHasErrors('email');

        $this->assertSame($before, Tenant::count(), 'a failed signup must not leave an orphan workspace');
    }
}
