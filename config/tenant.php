<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Central Domain
    |--------------------------------------------------------------------------
    |
    | The main domain of your application. Subdomains will be extracted
    | relative to this domain when using the subdomain resolver.
    |
    */
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'example.com'),

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | The Eloquent model that represents a tenant. This model must implement
    | the TenantContract interface.
    |
    */
    'tenant_model' => App\Models\Tenant::class,

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | The names of the database connections used for central and tenant
    | databases. These must be configured in config/database.php.
    |
    */
    'connections' => [
        'central' => env('TENANT_CONNECTION_CENTRAL', 'central'),
        'tenant' => env('TENANT_CONNECTION_TENANT', 'tenant'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Resolver
    |--------------------------------------------------------------------------
    |
    | Configuration for tenant resolution. The 'type' determines which
    | resolver will be used to identify tenants from incoming requests.
    |
    | Supported types: "subdomain", "path", "header"
    |
    */
    'resolver' => [
        'type' => env('TENANT_RESOLVER', 'subdomain'),

        // Custom resolver classes (you can override or add new ones)
        'drivers' => [
            'subdomain' => \UendelSilveira\TenantCore\Resolvers\SubdomainResolver::class,
            'path' => \UendelSilveira\TenantCore\Resolvers\PathResolver::class,
            'header' => \UendelSilveira\TenantCore\Resolvers\HeaderResolver::class,
        ],

        // Header name for the header resolver
        'header_name' => env('TENANT_HEADER_NAME', 'X-Tenant-ID'),
        'header_slug_name' => env('TENANT_HEADER_SLUG_NAME', 'X-Tenant-Slug'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Caching tenant lookups can significantly improve performance by
    | reducing database queries on every request.
    |
    */
    'cache' => [
        'enabled' => env('TENANT_CACHE_ENABLED', true),
        'ttl' => env('TENANT_CACHE_TTL', 3600), // seconds
        'prefix' => 'tenant',
        'store' => env('TENANT_CACHE_STORE', null), // null uses default cache store
    ],

];