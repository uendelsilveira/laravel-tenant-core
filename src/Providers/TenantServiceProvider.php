<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
use UendelSilveira\TenantCore\Contracts\TenantResolverContract;
use UendelSilveira\TenantCore\Context\TenantContext;
use UendelSilveira\TenantCore\Database\TenantDatabaseManager;
use UendelSilveira\TenantCore\Middleware\EnsureCentral;
use UendelSilveira\TenantCore\Middleware\EnsureTenant;
use UendelSilveira\TenantCore\Middleware\IdentifyTenant;
use UendelSilveira\TenantCore\Middleware\InitializeTenantDatabase;
use UendelSilveira\TenantCore\Resolvers\ResolverFactory;
use UendelSilveira\TenantCore\TenantManager;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/tenant.php', 'tenant');

        $this->registerContext();
        $this->registerDatabaseManager();
        $this->registerResolver();
        $this->registerManager();
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/tenant.php' => config_path('tenant.php'),
        ], 'tenant-config');

        $this->registerMiddleware();
        $this->registerOctaneSupport();
    }

    /**
     * Register the tenant context as singleton.
     */
    protected function registerContext(): void
    {
        $this->app->singleton(TenantContextContract::class, TenantContext::class);
    }

    /**
     * Register the database manager as singleton.
     */
    protected function registerDatabaseManager(): void
    {
        $this->app->singleton(TenantDatabaseManagerContract::class, TenantDatabaseManager::class);
    }

    /**
     * Register the tenant resolver.
     */
    protected function registerResolver(): void
    {
        $this->app->singleton(TenantResolverContract::class, function ($app) {
            return ResolverFactory::make();
        });
    }

    /**
     * Register the tenant manager facade accessor.
     */
    protected function registerManager(): void
    {
        $this->app->singleton('tenant', function ($app) {
            return new TenantManager(
                $app->make(TenantContextContract::class)
            );
        });
    }

    /**
     * Register middleware aliases and groups.
     */
    protected function registerMiddleware(): void
    {
        /** @var Router $router */
        $router = $this->app['router'];

        // Register middleware aliases
        $router->aliasMiddleware('tenant.identify', IdentifyTenant::class);
        $router->aliasMiddleware('tenant.database', InitializeTenantDatabase::class);
        $router->aliasMiddleware('tenant.ensure', EnsureTenant::class);
        $router->aliasMiddleware('tenant.central', EnsureCentral::class);

        // Register middleware groups
        $router->middlewareGroup('tenant', [
            IdentifyTenant::class,
            InitializeTenantDatabase::class,
            EnsureTenant::class,
        ]);

        $router->middlewareGroup('central', [
            IdentifyTenant::class,
            EnsureCentral::class,
        ]);
    }

    /**
     * Register Laravel Octane support for context cleanup.
     */
    protected function registerOctaneSupport(): void
    {
        if (!class_exists(\Laravel\Octane\Events\RequestTerminated::class)) {
            return;
        }

        $this->app['events']->listen(
            \Laravel\Octane\Events\RequestTerminated::class,
            function () {
                // Clear tenant context between requests in Octane
                $this->app->make(TenantContextContract::class)->clear();

                // Reset database connection to central
                $this->app->make(TenantDatabaseManagerContract::class)->disconnect();
            }
        );
    }
}