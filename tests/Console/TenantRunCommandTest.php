<?php

namespace UendelSilveira\TenantCore\Tests\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use UendelSilveira\TenantCore\Tests\Fixtures\Domain;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class TenantRunCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('database_name');
            $table->boolean('is_active')->default(true);
        });

        // Create domains table
        Schema::connection('central')->create('domains', function ($table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('domain');
            $table->boolean('is_primary')->default(false);
        });

        // Create test tenant with :memory: database for SQLite
        $tenant = Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'test',
            'database_name' => ':memory:',
        ]);

        Domain::on('central')->create([
            'tenant_id' => $tenant->id,
            'domain' => 'test',
            'is_primary' => true,
        ]);
    }

    #[Test]
    public function it_runs_command_for_specific_tenant()
    {
        $exitCode = Artisan::call('tenant:run', [
            'artisan_command' => 'list',
            '--tenant' => 'test',
        ]);

        $this->assertEquals(0, $exitCode);
    }

    #[Test]
    public function it_shows_error_when_no_tenants_found()
    {
        // Delete all tenants
        Tenant::on('central')->truncate();

        $exitCode = Artisan::call('tenant:run', [
            'artisan_command' => 'list',
        ]);

        $this->assertEquals(1, $exitCode);
        $this->assertStringContainsString('No tenants found', Artisan::output());
    }
}
