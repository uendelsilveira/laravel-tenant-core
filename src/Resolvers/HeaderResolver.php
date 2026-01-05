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

class HeaderResolver implements TenantResolverContract
{
    /**
     * Resolve the tenant from HTTP header.
     * Looks for X-Tenant-ID or X-Tenant-Slug header (configurable).
     */
    public function resolve(Request $request): ?TenantContract
    {
        $headerName = config('tenant.resolver.header_name', 'X-Tenant-ID');
        $headerSlugName = config('tenant.resolver.header_slug_name', 'X-Tenant-Slug');

        $tenantId = $request->header($headerName);
        $tenantSlug = $request->header($headerSlugName);

        if (! $tenantId && ! $tenantSlug) {
            return null;
        }

        if ($tenantId) {
            return $this->findTenantById($tenantId);
        }

        return $this->findTenantBySlug($tenantSlug);
    }

    /**
     * Find the tenant by ID with optional caching.
     */
    protected function findTenantById(string|int $id): ?TenantContract
    {
        if (! config('tenant.cache.enabled', true)) {
            return $this->queryTenantById($id);
        }

        $cacheKey = $this->getCacheKey('id', $id);
        $ttl = config('tenant.cache.ttl', 3600);
        $store = config('tenant.cache.store');

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($cacheKey, $ttl, fn () => $this->queryTenantById($id));
    }

    /**
     * Find the tenant by slug with optional caching.
     */
    protected function findTenantBySlug(string $slug): ?TenantContract
    {
        if (! config('tenant.cache.enabled', true)) {
            return $this->queryTenantBySlug($slug);
        }

        $cacheKey = $this->getCacheKey('slug', $slug);
        $ttl = config('tenant.cache.ttl', 3600);
        $store = config('tenant.cache.store');

        $cache = $store ? Cache::store($store) : Cache::store();

        return $cache->remember($cacheKey, $ttl, fn () => $this->queryTenantBySlug($slug));
    }

    /**
     * Query the tenant by ID from the database.
     */
    protected function queryTenantById(string|int $id): ?TenantContract
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        return $tenantModel::on($connection)
            ->where('id', $id)
            ->first();
    }

    /**
     * Query the tenant by slug from the database.
     */
    protected function queryTenantBySlug(string $slug): ?TenantContract
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        return $tenantModel::on($connection)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get the cache key.
     */
    protected function getCacheKey(string $type, string|int $value): string
    {
        $prefix = config('tenant.cache.prefix', 'tenant');

        return "{$prefix}:{$type}:{$value}";
    }
}
