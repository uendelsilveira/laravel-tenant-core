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
use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
use UendelSilveira\TenantCore\Events\TenantBooted;
use UendelSilveira\TenantCore\Events\TenantEnded;

class InitializeTenantDatabase
{
    public function __construct(
        protected TenantContextContract $context,
        protected TenantDatabaseManagerContract $databaseManager
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = $this->context->get();

        // If tenant exists, connect to tenant database
        if ($tenant) {
            $this->databaseManager->connect($tenant);

            // Dispatch TenantBooted event
            event(new TenantBooted($tenant));
        }

        $response = $next($request);

        // After request, disconnect if tenant was set
        if ($tenant) {
            // Dispatch TenantEnded event
            event(new TenantEnded($tenant));

            $this->databaseManager->disconnect();
        }

        return $response;
    }
}
