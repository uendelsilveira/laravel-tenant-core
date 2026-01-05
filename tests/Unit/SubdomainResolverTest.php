<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use UendelSilveira\TenantCore\Resolvers\SubdomainResolver;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class SubdomainResolverTest extends TestCase
{
    protected SubdomainResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new SubdomainResolver();
        
        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('domain');
            $table->string('database_name');
        });
    }

    /** @test */
    public function it_resolves_tenant_from_subdomain(): void
    {
        // Create a test tenant
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => 'tenant_acme'
        ]);

        $request = Request::create('http://acme.example.com/test');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNotNull($tenant);
        $this->assertEquals('acme', $tenant->domain);
    }

    /** @test */
    public function it_returns_null_for_central_domain(): void
    {
        $request = Request::create('http://example.com/test');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNull($tenant);
    }

    /** @test */
    public function it_returns_null_for_non_existent_tenant(): void
    {
        $request = Request::create('http://nonexistent.example.com/test');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNull($tenant);
    }
}

