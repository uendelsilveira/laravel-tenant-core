<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Events\TenantResolved;
use UendelSilveira\TenantCore\Middleware\IdentifyTenant;
use UendelSilveira\TenantCore\Tests\Fixtures\Domain;
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
            $table->string('database_name');
        });

        // Create domains table
        Schema::connection('central')->create('domains', function ($table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('domain');
            $table->boolean('is_primary')->default(false);
        });
    }

    #[Test]
    public function it_identifies_tenant_and_sets_context(): void
    {
        Event::fake();

        // Create a test tenant
        $tenant = Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'database_name' => 'tenant_acme',
        ]);

        // Create domain for tenant
        Domain::on('central')->create([
            'tenant_id' => $tenant->id,
            'domain' => 'acme',
            'is_primary' => true,
        ]);

        $request = Request::create('http://acme.example.com/test');
        $context = app(TenantContextContract::class);
        $middleware = new IdentifyTenant($context);

        $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertNotNull($context->get());
        $this->assertEquals('acme', $context->get()->getTenantDomain());

        Event::assertDispatched(TenantResolved::class);
    }

    #[Test]
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
