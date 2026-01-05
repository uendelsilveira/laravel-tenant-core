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

class PathResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from the first path segment.
     * Example: /tenant-slug/dashboard -> tenant-slug
     */
    public function resolve(Request $request): ?TenantContract
    {
        $tenantSlug = $this->extractSlug($request);

        if (!$tenantSlug) {
            return null;
        }

        return $this->findTenant($tenantSlug);
    }

    /**
     * Extract the tenant slug from the first path segment.
     */
    protected function extractSlug(Request $request): ?string
    {
        $segments = $request->segments();

        if (empty($segments)) {
            return null;
        }

        return $segments[0];
    }

    /**
     * Find the tenant by slug with optional caching.
     */
    protected function findTenant(string $slug): ?TenantContract
    {
        if (!config('tenant.cache.enabled', true)) {
            return $this->queryTenant($slug);
        }

        $cacheKey = $this->getCacheKey($slug);
        $ttl = config('tenant.cache.ttl', 3600);
        $store = config('tenant.cache.store');

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($cacheKey, $ttl, fn () => $this->queryTenant($slug));
    }

    /**
     * Query the tenant from the database.
     */
    protected function queryTenant(string $slug): ?TenantContract
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        return $tenantModel::on($connection)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get the cache key for a slug.
     */
    protected function getCacheKey(string $slug): string
    {
        $prefix = config('tenant.cache.prefix', 'tenant');
        return "{$prefix}:slug:{$slug}";
    }
}

