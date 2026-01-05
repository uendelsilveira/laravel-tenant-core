<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Resolvers;

use Illuminate\Http\Request;
use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Contracts\TenantResolverContract;

class HeaderResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from HTTP header.
     * Looks for X-Tenant-ID or X-Tenant-Slug header.
     *
     * @param Request $request
     * @return TenantContract|null
     */
    public function resolve(Request $request): ?TenantContract
    {
        // Try to get tenant ID from header
        $tenantId = $request->header('X-Tenant-ID');
        $tenantSlug = $request->header('X-Tenant-Slug');
        
        if (!$tenantId && !$tenantSlug) {
            return null;
        }
        
        // Query the tenant model from central database
        $tenantModel = config('tenant.tenant_model');
        
        // Ensure we're using the central connection
        $connection = config('tenant.connections.central', 'central');
        
        $query = $tenantModel::on($connection);
        
        if ($tenantId) {
            return $query->where('id', $tenantId)->first();
        }
        
        return $query->where('slug', $tenantSlug)->first();
    }
}

