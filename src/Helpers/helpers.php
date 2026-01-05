<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Facades\Tenant;

if (! function_exists('tenant')) {
    /**
     * Get the tenant manager instance or current tenant.
     *
     * @return \UendelSilveira\TenantCore\TenantManager|TenantContract|null
     */
    function tenant(): mixed
    {
        return app('tenant');
    }
}

if (! function_exists('tenant_current')) {
    /**
     * Get the current tenant.
     */
    function tenant_current(): ?TenantContract
    {
        return Tenant::current();
    }
}

if (! function_exists('tenant_key')) {
    /**
     * Get the current tenant's key.
     */
    function tenant_key(): string|int|null
    {
        return Tenant::key();
    }
}

if (! function_exists('tenant_slug')) {
    /**
     * Get the current tenant's slug.
     */
    function tenant_slug(): ?string
    {
        return Tenant::slug();
    }
}

if (! function_exists('is_central')) {
    /**
     * Check if the current context is central (no tenant).
     */
    function is_central(): bool
    {
        return Tenant::isCentral();
    }
}

if (! function_exists('is_tenant')) {
    /**
     * Check if the current context has a tenant.
     */
    function is_tenant(): bool
    {
        return Tenant::isTenant();
    }
}

// Keep old function names for backwards compatibility
if (! function_exists('tenant_is_central')) {
    /**
     * @deprecated Use is_central() instead
     */
    function tenant_is_central(): bool
    {
        return is_central();
    }
}

if (! function_exists('tenant_is_tenant')) {
    /**
     * @deprecated Use is_tenant() instead
     */
    function tenant_is_tenant(): bool
    {
        return is_tenant();
    }
}
