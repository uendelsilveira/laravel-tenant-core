<?php

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use UendelSilveira\TenantCore\Resolvers\HeaderResolver;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class HeaderResolverTest extends TestCase
{
    protected HeaderResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new HeaderResolver();
        
        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('domain');
            $table->string('database_name');
        });
    }

    /** @test */
    public function it_resolves_tenant_from_id_header(): void
    {
        // Create a test tenant
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => 'tenant_acme'
        ]);

        $request = Request::create('http://example.com/api/test');
        $request->headers->set('X-Tenant-ID', '1');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNotNull($tenant);
        $this->assertEquals(1, $tenant->id);
    }

    /** @test */
    public function it_resolves_tenant_from_slug_header(): void
    {
        // Create a test tenant
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => 'tenant_acme'
        ]);

        $request = Request::create('http://example.com/api/test');
        $request->headers->set('X-Tenant-Slug', 'acme');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNotNull($tenant);
        $this->assertEquals('acme', $tenant->slug);
    }

    /** @test */
    public function it_returns_null_when_no_header_present(): void
    {
        $request = Request::create('http://example.com/api/test');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNull($tenant);
    }

    /** @test */
    public function it_returns_null_for_non_existent_tenant(): void
    {
        $request = Request::create('http://example.com/api/test');
        $request->headers->set('X-Tenant-ID', '999');
        
        $tenant = $this->resolver->resolve($request);
        
        $this->assertNull($tenant);
    }
}

