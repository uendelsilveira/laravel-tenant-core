<?php

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use UendelSilveira\TenantCore\Database\TenantDatabaseManager;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class TenantDatabaseManagerTest extends TestCase
{
    protected TenantDatabaseManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TenantDatabaseManager();
    }

    /** @test */
    public function it_can_connect_to_tenant_database(): void
    {
        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'test-tenant',
            'database_name' => 'tenant_test_db'
        ]);

        $this->manager->connect($tenant);

        $this->assertEquals('tenant_test_db', Config::get('database.connections.tenant.database'));
        $this->assertEquals('tenant', DB::getDefaultConnection());
    }

    /** @test */
    public function it_can_disconnect_from_tenant_database(): void
    {
        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'test-tenant',
            'database_name' => 'tenant_test_db'
        ]);

        // First connect
        $this->manager->connect($tenant);
        $this->assertEquals('tenant', DB::getDefaultConnection());

        // Then disconnect
        $this->manager->disconnect();
        $this->assertEquals('central', DB::getDefaultConnection());
    }
}

