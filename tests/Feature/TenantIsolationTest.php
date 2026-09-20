<?php

namespace Tests\Feature;

use App\Models\Appointments;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Row-level isolation: every tenant-owned model carries tenant_id and is
 * filtered by the global TenantScope, so one tenant can never read or write
 * another tenant's rows. Super admins run unscoped and see everything.
 */
class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private TenantManager $tenants;
    private Tenant $tenantA;
    private Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenants = app(TenantManager::class);
        $this->tenantA = Tenant::factory()->create(['name' => 'Tenant A']);
        $this->tenantB = Tenant::factory()->create(['name' => 'Tenant B']);
    }

    private function appointmentFor(Tenant $tenant, string $name): Appointments
    {
        return $this->tenants->runForTenant($tenant, fn () => Appointments::create([
            'name' => $name,
            'phone' => '910000000000',
            'date' => '2026-01-01',
            'time' => '10:00 AM - 10:30 AM',
            'status' => 1,
        ]));
    }

    public function test_tenant_id_is_auto_filled_from_the_current_context(): void
    {
        $appointment = $this->appointmentFor($this->tenantA, 'Ada');

        $this->assertSame($this->tenantA->id, $appointment->tenant_id);
    }

    public function test_reads_are_scoped_to_the_current_tenant(): void
    {
        $this->appointmentFor($this->tenantA, 'Ada');
        $this->appointmentFor($this->tenantB, 'Grace');

        $this->tenants->set($this->tenantA);

        $names = Appointments::pluck('name')->all();

        $this->assertSame(['Ada'], $names, 'tenant A must only see its own rows');
    }

    public function test_a_tenant_cannot_read_another_tenants_row_by_id(): void
    {
        $foreign = $this->appointmentFor($this->tenantB, 'Grace');

        $this->tenants->set($this->tenantA);

        $this->assertNull(
            Appointments::find($foreign->id),
            'a row belonging to tenant B must not be reachable from tenant A'
        );
    }

    public function test_a_tenant_cannot_update_another_tenants_row(): void
    {
        $foreign = $this->appointmentFor($this->tenantB, 'Grace');

        $this->tenants->set($this->tenantA);

        $affected = Appointments::where('id', $foreign->id)->update(['name' => 'Hijacked']);

        $this->assertSame(0, $affected, 'the scoped update must not touch tenant B rows');
        $this->assertSame('Grace', $foreign->fresh()->name);
    }

    public function test_a_tenant_cannot_delete_another_tenants_row(): void
    {
        $foreign = $this->appointmentFor($this->tenantB, 'Grace');

        $this->tenants->set($this->tenantA);

        $deleted = Appointments::where('id', $foreign->id)->delete();

        $this->assertSame(0, $deleted);
        $this->assertNotNull($foreign->fresh(), 'tenant B row must survive');
    }

    public function test_super_admin_bypass_sees_every_tenant(): void
    {
        $this->appointmentFor($this->tenantA, 'Ada');
        $this->appointmentFor($this->tenantB, 'Grace');

        $this->tenants->set($this->tenantA);
        $this->tenants->bypass(true);

        $names = Appointments::orderBy('name')->pluck('name')->all();

        $this->assertSame(['Ada', 'Grace'], $names, 'super admin sees all tenants');
    }

    public function test_counts_are_isolated_per_tenant(): void
    {
        $this->appointmentFor($this->tenantA, 'Ada');
        $this->appointmentFor($this->tenantB, 'Grace');
        $this->appointmentFor($this->tenantB, 'Alan');

        $a = $this->tenants->runForTenant($this->tenantA, fn () => Appointments::count());
        $b = $this->tenants->runForTenant($this->tenantB, fn () => Appointments::count());

        $this->assertSame(1, $a);
        $this->assertSame(2, $b);
    }

    public function test_users_are_attached_to_their_tenant(): void
    {
        $user = User::factory()->forTenant($this->tenantA)->create();

        $this->assertSame($this->tenantA->id, $user->tenant_id);
        $this->assertTrue($user->tenant->is($this->tenantA));
    }

    public function test_super_admin_belongs_to_no_tenant(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->assertNull($admin->tenant_id);
        $this->assertTrue($admin->isSuperAdmin());
    }
}
