<?php

use App\Models\Tenant;

if (! function_exists('tenant')) {
    /**
     * Get the current tenant from the application container.
     */
    function tenant(): ?Tenant
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }
}

if (! function_exists('tenant_id')) {
    /**
     * Get current tenant ID.
     */
    function tenant_id(): ?int
    {
        return tenant()?->id;
    }
}

if (! function_exists('is_tenant_request')) {
    /**
     * Check if the request is for a specific tenant.
     */
    function is_tenant_request(): bool
    {
        return tenant() !== null;
    }
}

if (! function_exists('get_active_tenants')) {
    /**
     * Get all active tenants.
     */
    function get_active_tenants()
    {
        return Tenant::where('is_active', true)->get();
    }
}
