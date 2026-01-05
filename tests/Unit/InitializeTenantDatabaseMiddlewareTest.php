<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
use UendelSilveira\TenantCore\Events\TenantBooted;
use UendelSilveira\TenantCore\Events\TenantEnded;
use UendelSilveira\TenantCore\Middleware\InitializeTenantDatabase;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class InitializeTenantDatabaseMiddlewareTest extends TestCase
{
    /** @test */
    public function it_connects_to_tenant_database_when_tenant_is_set(): void
    {
        Event::fake();

        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'acme',
            'database_name' => ':memory:'
        ]);

        $context = app(TenantContextContract::class);
        $context->set($tenant);

        $dbManager = app(TenantDatabaseManagerContract::class);
        $middleware = new InitializeTenantDatabase($context, $dbManager);

        $request = Request::create('http://acme.example.com/test');

        $middleware->handle($request, function ($req) {
            // During request, should be connected to tenant
            $this->assertEquals('tenant', DB::getDefaultConnection());
            return response('OK');
        });

        // After request, should be disconnected
        $this->assertEquals('central', DB::getDefaultConnection());

        Event::assertDispatched(TenantBooted::class);
        Event::assertDispatched(TenantEnded::class);
    }

    /** @test */
    public function it_does_not_connect_when_no_tenant_is_set(): void
    {
        Event::fake();

        $context = app(TenantContextContract::class);
        $dbManager = app(TenantDatabaseManagerContract::class);
        $middleware = new InitializeTenantDatabase($context, $dbManager);

        $request = Request::create('http://example.com/test');

        $middleware->handle($request, function ($req) {
            // Should remain on central
            $this->assertEquals('central', DB::getDefaultConnection());
            return response('OK');
        });

        $this->assertEquals('central', DB::getDefaultConnection());

        Event::assertNotDispatched(TenantBooted::class);
        Event::assertNotDispatched(TenantEnded::class);
    }
}

