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
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Events\TenantResolved;
use UendelSilveira\TenantCore\Resolvers\ResolverFactory;

class IdentifyTenant
{
    public function __construct(
        protected TenantContextContract $context
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Get the resolver from factory
        $resolver = ResolverFactory::make();

        // Resolve the tenant
        $tenant = $resolver->resolve($request);

        // Set tenant in context
        if ($tenant) {
            $this->context->set($tenant);

            // Dispatch TenantResolved event
            event(new TenantResolved($tenant));
        }

        return $next($request);
    }
}