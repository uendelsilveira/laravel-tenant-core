<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantMigrateRollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate:rollback 
                            {--tenant= : Rollback migrations for specific tenant slug}
                            {--step=1 : Number of migrations to rollback}
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback migrations for tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenants = $this->getTenants();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');

            return self::FAILURE;
        }

        $this->warn("Rolling back migrations for {$tenants->count()} tenant(s)...");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->rollbackTenant($tenant);
        }

        $this->info('All rollbacks completed!');

        return self::SUCCESS;
    }

    protected function getTenants()
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        $query = $tenantModel::on($connection)->where('is_active', true);

        if ($tenantSlug = $this->option('tenant')) {
            $query->where('slug', $tenantSlug);
        }

        return $query->get();
    }

    protected function rollbackTenant($tenant): void
    {
        $this->line("Rolling back tenant: <info>{$tenant->getTenantSlug()}</info> (Database: {$tenant->getTenantDatabase()})");

        config(['database.connections.tenant.database' => $tenant->getTenantDatabase()]);
        DB::purge('tenant');

        try {
            Artisan::call('migrate:rollback', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--step' => $this->option('step'),
                '--force' => $this->option('force'),
            ]);

            $this->line(trim(Artisan::output()));
            $this->info("✓ Completed: {$tenant->getTenantSlug()}");
        } catch (\Exception $e) {
            $this->error("✗ Failed: {$tenant->getTenantSlug()}");
            $this->error($e->getMessage());
        }

        $this->newLine();
    }
}
