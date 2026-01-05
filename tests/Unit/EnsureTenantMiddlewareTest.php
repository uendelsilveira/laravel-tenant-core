<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Middleware\EnsureTenant;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class EnsureTenantMiddlewareTest extends TestCase
{
    /** @test */
    public function it_allows_request_when_tenant_is_set(): void
    {
        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'acme',
            'database_name' => 'tenant_acme',
        ]);

        $context = app(TenantContextContract::class);
        $context->set($tenant);

        $middleware = new EnsureTenant($context);
        $request = Request::create('http://acme.example.com/test');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_throws_exception_when_no_tenant_is_set(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Tenant not found.');

        $context = app(TenantContextContract::class);
        $middleware = new EnsureTenant($context);
        $request = Request::create('http://example.com/test');

        $middleware->handle($request, function ($req) {
            return response('OK');
        });
    }
}
