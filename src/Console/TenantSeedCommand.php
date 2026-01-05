<?php

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed 
                            {--tenant= : Seed specific tenant slug}
                            {--class= : The seeder class to run}
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed databases for all tenants or a specific tenant';

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

        $this->info("Seeding {$tenants->count()} tenant(s)...");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->seedTenant($tenant);
        }

        $this->info('All seeding completed!');

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

    protected function seedTenant($tenant): void
    {
        $this->line("Seeding tenant: <info>{$tenant->getTenantSlug()}</info> (Database: {$tenant->getTenantDatabase()})");

        config(['database.connections.tenant.database' => $tenant->getTenantDatabase()]);
        DB::purge('tenant');

        try {
            $params = [
                '--database' => 'tenant',
                '--force' => $this->option('force'),
            ];

            if ($class = $this->option('class')) {
                $params['--class'] = $class;
            }

            Artisan::call('db:seed', $params);

            $this->line(trim(Artisan::output()));
            $this->info("✓ Completed: {$tenant->getTenantSlug()}");
        } catch (\Exception $e) {
            $this->error("✗ Failed: {$tenant->getTenantSlug()}");
            $this->error($e->getMessage());
        }

        $this->newLine();
    }
}
