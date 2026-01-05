<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
    public function it_can_connect_to_tenant_database(): void
    {
        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'test-tenant',
            'database_name' => ':memory:',
        ]);

        $this->manager->connect($tenant);

        $this->assertEquals(':memory:', Config::get('database.connections.tenant.database'));
        $this->assertEquals('tenant', DB::getDefaultConnection());
    }

    #[Test]
    public function it_can_disconnect_from_tenant_database(): void
    {
        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'test-tenant',
            'database_name' => ':memory:',
        ]);

        // First connect
        $this->manager->connect($tenant);
        $this->assertEquals('tenant', DB::getDefaultConnection());

        // Then disconnect
        $this->manager->disconnect();
        $this->assertEquals('central', DB::getDefaultConnection());
    }

    #[Test]
    public function it_throws_exception_for_empty_database(): void
    {
        $this->expectException(\UendelSilveira\TenantCore\Exceptions\TenantDatabaseException::class);

        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'test-tenant',
            'database_name' => '',
        ]);

        $this->manager->connect($tenant);
    }
}
