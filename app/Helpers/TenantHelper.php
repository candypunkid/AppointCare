<?php

use App\Models\Tenant;

/**
 * Get the current tenant from the application container.
 */
function tenant(): ?Tenant
{
    return app()->bound('tenant') ? app('tenant') : null;
}

/**
 * Get current tenant ID.
 */
function tenant_id(): ?int
{
    return tenant()?->id;
}

/**
 * Check if the request is for a specific tenant.
 */
function is_tenant_request(): bool
{
    return tenant() !== null;
}

/**
 * Get all active tenants.
 */
function get_active_tenants()
{
    return Tenant::where('is_active', true)->get();
}
