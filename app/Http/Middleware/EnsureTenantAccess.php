<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Allow through if the user is a super admin or if they belong to a tenant
        $isSuper = method_exists($user, 'hasRole') ? $user->hasRole('super_admin') : ($user->role === 'super_admin');

        if ($isSuper || $user->tenant_id) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Tenant context missing.');
    }
}
