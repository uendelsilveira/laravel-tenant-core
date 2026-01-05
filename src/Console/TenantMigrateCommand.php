<?php

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate 
                            {--tenant= : Run migrations for specific tenant slug}
                            {--seed : Run seeders after migrations}
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenants or a specific tenant';

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

        $this->info("Running migrations for {$tenants->count()} tenant(s)...");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->migrateTenant($tenant);
        }

        $this->info('All migrations completed!');

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

    protected function migrateTenant($tenant): void
    {
        $this->line("Migrating tenant: <info>{$tenant->getTenantSlug()}</info> (Database: {$tenant->getTenantDatabase()})");

        config(['database.connections.tenant.database' => $tenant->getTenantDatabase()]);
        DB::purge('tenant');

        try {
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => $this->option('force'),
            ]);

            $output = trim(Artisan::output());
            if ($output) {
                $this->line($output);
            }

            if ($this->option('seed')) {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--force' => $this->option('force'),
                ]);
                $this->line(trim(Artisan::output()));
            }

            $this->info("✓ Completed: {$tenant->getTenantSlug()}");
        } catch (\Exception $e) {
            $this->error("✗ Failed: {$tenant->getTenantSlug()}");
            $this->error($e->getMessage());
        }

        $this->newLine();
    }
}
