<?php

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Events\TenantResolved;
use UendelSilveira\TenantCore\Middleware\IdentifyTenant;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class IdentifyTenantMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('domain');
            $table->string('database_name');
        });
    }

    /** @test */
    public function it_identifies_tenant_and_sets_context(): void
    {
        Event::fake();
        
        // Create a test tenant
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => 'tenant_acme'
        ]);

        $request = Request::create('http://acme.example.com/test');
        $context = app(TenantContextContract::class);
        $middleware = new IdentifyTenant($context);

        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertNotNull($context->get());
        $this->assertEquals('acme', $context->get()->domain);
        
        Event::assertDispatched(TenantResolved::class);
    }

    /** @test */
    public function it_does_not_set_context_for_central_domain(): void
    {
        Event::fake();
        
        $request = Request::create('http://example.com/test');
        $context = app(TenantContextContract::class);
        $middleware = new IdentifyTenant($context);

        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertNull($context->get());
        $this->assertTrue($context->isCentral());
        
        Event::assertNotDispatched(TenantResolved::class);
    }
}

