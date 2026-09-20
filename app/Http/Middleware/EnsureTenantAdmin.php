<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to the people who administer a workspace: the tenant's own
 * admin, or a platform super admin acting across tenants.
 *
 * Tenant staff (doctors) are deliberately excluded — they use the workspace,
 * but connecting or disconnecting the WhatsApp number that the whole tenant
 * sends from is an owner-level action.
 */
class EnsureTenantAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !($user->isSuperAdmin() || $user->isTenantAdmin())) {
            abort(403, 'Workspace administrator access required.');
        }

        return $next($request);
    }
}
