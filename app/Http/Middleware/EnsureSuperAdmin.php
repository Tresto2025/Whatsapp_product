<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to super-admin users.
 *
 * Phase 0 stepping stone: uses the legacy `users.role == 1` convention. Phase 1
 * replaces this check with spatie/laravel-permission roles ("super_admin").
 */
class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || (int) $user->role !== 1) {
            abort(403, 'Super admin access required.');
        }

        return $next($request);
    }
}
