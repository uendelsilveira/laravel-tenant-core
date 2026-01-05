<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Resolvers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Contracts\TenantResolverContract;

class SubdomainResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from the subdomain.
     */
    public function resolve(Request $request): ?TenantContract
    {
        $subdomain = $this->extractSubdomain($request);

        if (!$subdomain) {
            return null;
        }

        return $this->findTenant($subdomain);
    }

    /**
     * Extract the subdomain from the request.
     */
    protected function extractSubdomain(Request $request): ?string
    {
        $host = $request->getHost();
        $centralDomain = config('tenant.central_domain');

        // Remove the central domain to get the subdomain
        $subdomain = str_replace('.' . $centralDomain, '', $host);

        // If subdomain is the same as host or equals central domain, no tenant
        if ($subdomain === $host || $subdomain === $centralDomain || empty($subdomain)) {
            return null;
        }

        return $subdomain;
    }

    /**
     * Find the tenant by subdomain with optional caching.
     */
    protected function findTenant(string $subdomain): ?TenantContract
    {
        if (!config('tenant.cache.enabled', true)) {
            return $this->queryTenant($subdomain);
        }

        $cacheKey = $this->getCacheKey($subdomain);
        $ttl = config('tenant.cache.ttl', 3600);
        $store = config('tenant.cache.store');

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($cacheKey, $ttl, fn () => $this->queryTenant($subdomain));
    }

    /**
     * Query the tenant from the database.
     */
    protected function queryTenant(string $subdomain): ?TenantContract
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        return $tenantModel::on($connection)
            ->where('domain', $subdomain)
            ->first();
    }

    /**
     * Get the cache key for a subdomain.
     */
    protected function getCacheKey(string $subdomain): string
    {
        $prefix = config('tenant.cache.prefix', 'tenant');
        return "{$prefix}:domain:{$subdomain}";
    }
}

