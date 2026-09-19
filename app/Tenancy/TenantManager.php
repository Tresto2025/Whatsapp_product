<?php

namespace App\Tenancy;

use App\Models\Tenant;

/**
 * Holds the current tenant context for a request/job.
 *
 * Bound as a singleton in AppServiceProvider. The BelongsToTenant global scope
 * reads from here to filter queries; ResolveTenant middleware populates it from
 * the authenticated user, and the inbound-WhatsApp job (Phase 2) sets it from
 * the matched whatsapp_accounts row.
 */
class TenantManager
{
    private ?Tenant $tenant = null;

    /** When true the global tenant scope is bypassed (super admin / console). */
    private bool $bypassed = false;

    public function set(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function check(): bool
    {
        return $this->tenant !== null;
    }

    public function bypass(bool $state = true): void
    {
        $this->bypassed = $state;
    }

    public function isBypassed(): bool
    {
        return $this->bypassed;
    }

    /**
     * Whether the tenant global scope should currently be applied.
     */
    public function shouldScope(): bool
    {
        return !$this->bypassed && $this->tenant !== null;
    }

    /**
     * Run a callback with the tenant scope disabled, restoring state after.
     */
    public function runWithoutScope(callable $callback): mixed
    {
        $previous = $this->bypassed;
        $this->bypassed = true;

        try {
            return $callback();
        } finally {
            $this->bypassed = $previous;
        }
    }

    /**
     * Run a callback scoped to a specific tenant, restoring state after.
     */
    public function runForTenant(Tenant $tenant, callable $callback): mixed
    {
        $previousTenant = $this->tenant;
        $previousBypass = $this->bypassed;

        $this->tenant = $tenant;
        $this->bypassed = false;

        try {
            return $callback();
        } finally {
            $this->tenant = $previousTenant;
            $this->bypassed = $previousBypass;
        }
    }
}
