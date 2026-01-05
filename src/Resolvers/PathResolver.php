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

class PathResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from the first path segment.
     * Example: /tenant-slug/dashboard -> tenant-slug
     *
     * @param Request $request
     * @return TenantContract|null
     */
    public function resolve(Request $request): ?TenantContract
    {
        $segments = $request->segments();
        
        // No segments means no tenant
        if (empty($segments)) {
            return null;
        }
        
        // Get the first segment as tenant slug
        $tenantSlug = $segments[0];
        
        // Query the tenant model from central database
        $tenantModel = config('tenant.tenant_model');
        
        // Ensure we're using the central connection
        $connection = config('tenant.connections.central', 'central');
        
        return $tenantModel::on($connection)
            ->where('slug', $tenantSlug)
            ->first();
    }
}

