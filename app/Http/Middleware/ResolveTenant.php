<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Establishes the current tenant context for authenticated web requests.
 *
 * - Super admins bypass the tenant scope (they see all tenants).
 * - Every other user is pinned to their own tenant_id; if a suspended or
 *   missing tenant is resolved, access is denied.
 * - Guests are left unscoped (public marketing pages have no tenant data).
 */
class ResolveTenant
{
    public function __construct(private TenantManager $tenants)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            $this->tenants->bypass(true);
            return $next($request);
        }

        $tenant = $user->tenant;

        if (!$tenant) {
            abort(403, 'Your account is not linked to a workspace.');
        }

        if (!$tenant->isActive()) {
            abort(403, 'This workspace is not active. Please contact support.');
        }

        $this->tenants->set($tenant);

        return $next($request);
    }
}
