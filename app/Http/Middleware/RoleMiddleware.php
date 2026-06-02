<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access control middleware.
 * Usage in routes: ->middleware('role:admin,pengurus')
 *
 * Supported roles: admin, pengurus, kasir, anggota
 *
 * Security: Validates role server-side from authenticated session.
 * Never trusts client-supplied role data.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Allowed roles (variadic)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Fail safe: deny if not authenticated
        if (! $request->user()) {
            abort(401, 'Unauthenticated.');
        }

        $userRole = $request->user()->role;

        // Deny if user's role is not in the allowed list
        if (! in_array($userRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
