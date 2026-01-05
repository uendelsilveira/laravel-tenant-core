<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use UendelSilveira\TenantCore\Providers\TenantServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            TenantServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'central');
        $app['config']->set('database.connections.central', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('database.connections.tenant', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        // Setup tenant configuration
        $app['config']->set('tenant.central_domain', 'example.com');
        $app['config']->set('tenant.tenant_model', \UendelSilveira\TenantCore\Tests\Fixtures\Tenant::class);
        $app['config']->set('tenant.connections.central', 'central');
        $app['config']->set('tenant.connections.tenant', 'tenant');
        $app['config']->set('tenant.resolver.type', 'subdomain');

        // Disable cache for tests
        $app['config']->set('tenant.cache.enabled', false);
    }
}

