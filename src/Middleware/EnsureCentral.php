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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;

class EnsureCentral
{
    public function __construct(
        protected TenantContextContract $context
    ) {}

    /**
     * Handle an incoming request.
     * Ensures NO tenant is present in the context (central domain only).
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AccessDeniedHttpException
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!$this->context->isCentral()) {
            throw new AccessDeniedHttpException('This route is only accessible from the central domain.');
        }

        return $next($request);
    }
}