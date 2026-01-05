<?php

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;

class TenantListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:list {--active : Show only active tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        $query = $tenantModel::on($connection)->with('domains');

        if ($this->option('active')) {
            $query->where('is_active', true);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');
            return self::SUCCESS;
        }

        $headers = ['ID', 'Name', 'Slug', 'Domains', 'Database', 'Active'];
        $rows = [];

        foreach ($tenants as $tenant) {
            $domains = $tenant->domains->pluck('domain')->join(', ') ?: '-';
            
            $rows[] = [
                $tenant->getTenantKey(),
                $tenant->name,
                $tenant->getTenantSlug(),
                $domains,
                $tenant->getTenantDatabase(),
                $tenant->is_active ? '✓' : '✗',
            ];
        }

        $this->table($headers, $rows);
        $this->info("Total: {$tenants->count()} tenant(s)");

        return self::SUCCESS;
    }
}
