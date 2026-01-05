<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;

class EnsureTenant
{
    public function __construct(
        protected TenantContextContract $context
    ) {}

    /**
     * Handle an incoming request.
     * Ensures a tenant is present in the context.
     *
     * @throws NotFoundHttpException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->context->isCentral()) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        return $next($request);
    }
}
