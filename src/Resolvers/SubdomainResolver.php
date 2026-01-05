<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Resolvers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Contracts\TenantResolverContract;

class SubdomainResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from the subdomain.
     *
     * @param Request $request
     * @return TenantContract|null
     */
    public function resolve(Request $request): ?TenantContract
    {
        $host = $request->getHost();
        $centralDomain = config('tenant.central_domain');
        
        // Remove the central domain to get the subdomain
        $subdomain = str_replace('.' . $centralDomain, '', $host);
        
        // If subdomain is the same as host or equals central domain, no tenant
        if ($subdomain === $host || $subdomain === $centralDomain || empty($subdomain)) {
            return null;
        }
        
        // Query the tenant model from central database
        $tenantModel = config('tenant.tenant_model');
        
        // Ensure we're using the central connection
        $connection = config('tenant.connections.central', 'central');
        
        return $tenantModel::on($connection)
            ->where('domain', $subdomain)
            ->first();
    }
}

