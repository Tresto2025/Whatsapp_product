<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Conversation;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WhatsappAccount;
use App\Tenancy\TenantManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Row-level isolation: every tenant-owned model carries tenant_id and is
 * filtered by the global scope, so one workspace can never read or write
 * another's rows. Super admins run unscoped.
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

    private function contactFor(Tenant $tenant, string $waId, string $name): Contact
    {
        return $this->tenants->runForTenant(
            $tenant,
            fn () => Contact::create(['wa_id' => $waId, 'name' => $name])
        );
    }

    public function test_tenant_id_is_auto_filled_from_the_current_context(): void
    {
        $contact = $this->contactFor($this->tenantA, '919000000001', 'Ada');

        $this->assertSame($this->tenantA->id, $contact->tenant_id);
    }

    public function test_reads_are_scoped_to_the_current_tenant(): void
    {
        $this->contactFor($this->tenantA, '919000000001', 'Ada');
        $this->contactFor($this->tenantB, '919000000002', 'Grace');

        $this->tenants->set($this->tenantA);

        $this->assertSame(['Ada'], Contact::pluck('name')->all());
    }

    public function test_a_tenant_cannot_read_another_tenants_row_by_id(): void
    {
        $foreign = $this->contactFor($this->tenantB, '919000000002', 'Grace');

        $this->tenants->set($this->tenantA);

        $this->assertNull(Contact::find($foreign->id));
    }

    public function test_a_tenant_cannot_update_another_tenants_row(): void
    {
        $foreign = $this->contactFor($this->tenantB, '919000000002', 'Grace');

        $this->tenants->set($this->tenantA);

        $affected = Contact::where('id', $foreign->id)->update(['name' => 'Hijacked']);

        $this->assertSame(0, $affected);
        $this->assertSame('Grace', $foreign->fresh()->name);
    }

    public function test_a_tenant_cannot_delete_another_tenants_row(): void
    {
        $foreign = $this->contactFor($this->tenantB, '919000000002', 'Grace');

        $this->tenants->set($this->tenantA);

        $this->assertSame(0, Contact::where('id', $foreign->id)->delete());
        $this->assertNotNull($foreign->fresh());
    }

    public function test_the_same_person_messaging_two_tenants_is_two_contacts(): void
    {
        $a = $this->contactFor($this->tenantA, '919000000001', 'Ada');
        $b = $this->contactFor($this->tenantB, '919000000001', 'Ada');

        $this->assertNotSame($a->id, $b->id);
        $this->assertSame($this->tenantA->id, $a->tenant_id);
        $this->assertSame($this->tenantB->id, $b->tenant_id);
    }

    public function test_conversations_are_isolated_too(): void
    {
        $contactB = $this->contactFor($this->tenantB, '919000000002', 'Grace');
        $accountB = WhatsappAccount::factory()->forTenant($this->tenantB)->create();

        $conversation = $this->tenants->runForTenant($this->tenantB, fn () => Conversation::create([
            'contact_id' => $contactB->id,
            'whatsapp_account_id' => $accountB->id,
        ]));

        $this->tenants->set($this->tenantA);

        $this->assertNull(Conversation::find($conversation->id));
    }

    public function test_super_admin_bypass_sees_every_tenant(): void
    {
        $this->contactFor($this->tenantA, '919000000001', 'Ada');
        $this->contactFor($this->tenantB, '919000000002', 'Grace');

        $this->tenants->set($this->tenantA);
        $this->tenants->bypass(true);

        $this->assertSame(['Ada', 'Grace'], Contact::orderBy('name')->pluck('name')->all());
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
