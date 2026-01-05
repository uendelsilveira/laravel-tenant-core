<?php

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantMigrateFreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate:fresh 
                            {--tenant= : Run migrations for specific tenant slug}
                            {--seed : Run seeders after migrations}
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run migrations for tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('This will DROP ALL TABLES in tenant databases. Are you sure?')) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $tenants = $this->getTenants();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');
            return self::FAILURE;
        }

        $this->warn("Running fresh migrations for {$tenants->count()} tenant(s)...");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->freshMigrateTenant($tenant);
        }

        $this->info('All fresh migrations completed!');

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

    protected function freshMigrateTenant($tenant): void
    {
        $this->line("Fresh migrating tenant: <info>{$tenant->getTenantSlug()}</info> (Database: {$tenant->getTenantDatabase()})");

        config(['database.connections.tenant.database' => $tenant->getTenantDatabase()]);
        DB::purge('tenant');

        try {
            Artisan::call('migrate:fresh', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $this->line(trim(Artisan::output()));

            if ($this->option('seed')) {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--force' => true,
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
