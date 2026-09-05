<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the host from the request
        $host = $request->getHost();

        // If running on local host, skip tenant resolution
        if (in_array($host, ['127.0.0.1', '::1', 'localhost'])) {
            return $next($request);
        }

        // Extract subdomain (everything before the first dot)
        $parts = explode('.', $host);
        $subdomain = count($parts) > 1 ? $parts[0] : null;

        // Skip if it's a reserved subdomain (like www, mail, etc.)
        $reservedSubdomains = ['www', 'mail', 'ftp', 'admin', 'api'];

        if ($subdomain && ! in_array($subdomain, $reservedSubdomains)) {
            // Try to find tenant by slug (subdomain)
            $tenant = Tenant::where('slug', $subdomain)
                ->where('is_active', true)
                ->first();

            if ($tenant) {
                app()->instance('tenant', $tenant);

                // Set the tenant context in the request
                $request->attributes->set('tenant', $tenant);

                // Share tenant with views
                view()->share('currentTenant', $tenant);

                return $next($request);
            }
        }

        // If no tenant found, try to find by custom domain
        $tenant = Tenant::where('domain', $host)
            ->where('is_active', true)
            ->first();

        if ($tenant) {
            app()->instance('tenant', $tenant);
            $request->attributes->set('tenant', $tenant);
            view()->share('currentTenant', $tenant);
        } else {
            // If no tenant found, return 404 or redirect
            abort(404, 'Tenant not found');
        }

        return $next($request);
    }
}
