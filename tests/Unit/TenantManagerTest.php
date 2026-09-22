<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Tenancy\TenantManager;
use PHPUnit\Framework\TestCase;

class TenantManagerTest extends TestCase
{
    private function tenant(int $id): Tenant
    {
        $t = new Tenant(['name' => "Tenant {$id}", 'slug' => "t{$id}"]);
        $t->id = $id;

        return $t;
    }

    public function test_no_scope_without_a_tenant(): void
    {
        $m = new TenantManager();

        $this->assertFalse($m->check());
        $this->assertNull($m->id());
        $this->assertFalse($m->shouldScope());
    }

    public function test_scopes_once_a_tenant_is_set(): void
    {
        $m = new TenantManager();
        $m->set($this->tenant(5));

        $this->assertTrue($m->check());
        $this->assertSame(5, $m->id());
        $this->assertTrue($m->shouldScope());
    }

    public function test_bypass_disables_scoping(): void
    {
        $m = new TenantManager();
        $m->set($this->tenant(5));
        $m->bypass(true);

        $this->assertTrue($m->check());
        $this->assertFalse($m->shouldScope(), 'bypass should suspend scoping');
    }

    public function test_run_without_scope_restores_previous_state(): void
    {
        $m = new TenantManager();
        $m->set($this->tenant(5));

        $inside = $m->runWithoutScope(fn () => $m->shouldScope());

        $this->assertFalse($inside, 'scope suspended inside the callback');
        $this->assertTrue($m->shouldScope(), 'scope restored after the callback');
    }

    public function test_run_for_tenant_switches_then_restores(): void
    {
        $m = new TenantManager();
        $m->set($this->tenant(1));

        $innerId = $m->runForTenant($this->tenant(2), fn () => $m->id());

        $this->assertSame(2, $innerId, 'runs as the given tenant');
        $this->assertSame(1, $m->id(), 'restores the original tenant afterwards');
    }
}
