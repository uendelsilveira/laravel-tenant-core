<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use UendelSilveira\TenantCore\Resolvers\PathResolver;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class PathResolverTest extends TestCase
{
    protected PathResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new PathResolver();

        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('domain');
            $table->string('database_name');
        });
    }

    #[Test]
    public function it_resolves_tenant_from_path(): void
    {
        // Create a test tenant
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => 'tenant_acme',
        ]);

        $request = Request::create('http://example.com/acme/dashboard');

        $tenant = $this->resolver->resolve($request);

        $this->assertNotNull($tenant);
        $this->assertEquals('acme', $tenant->slug);
    }

    #[Test]
    public function it_returns_null_for_empty_path(): void
    {
        $request = Request::create('http://example.com/');

        $tenant = $this->resolver->resolve($request);

        $this->assertNull($tenant);
    }

    #[Test]
    public function it_returns_null_for_non_existent_tenant(): void
    {
        $request = Request::create('http://example.com/nonexistent/page');

        $tenant = $this->resolver->resolve($request);

        $this->assertNull($tenant);
    }
}
