<?php

namespace UendelSilveira\TenantCore\Tests\Unit;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Middleware\EnsureCentral;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class EnsureCentralMiddlewareTest extends TestCase
{
    /** @test */
    public function it_allows_request_when_no_tenant_is_set(): void
    {
        $context = app(TenantContextContract::class);
        $middleware = new EnsureCentral($context);
        $request = Request::create('http://example.com/test');

        $response = $middleware->handle($request, function ($req) {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    /** @test */
    public function it_throws_exception_when_tenant_is_set(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('This route is only accessible from the central domain.');

        $tenant = new Tenant([
            'id' => 1,
            'slug' => 'acme',
            'database_name' => 'tenant_acme'
        ]);

        $context = app(TenantContextContract::class);
        $context->set($tenant);

        $middleware = new EnsureCentral($context);
        $request = Request::create('http://acme.example.com/test');

        $middleware->handle($request, function ($req) {
            return response('OK');
        });
    }
}

