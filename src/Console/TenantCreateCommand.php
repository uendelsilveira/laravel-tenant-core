<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create 
                            {name : The tenant name}
                            {--slug= : The tenant slug (auto-generated if not provided)}
                            {--domain= : The tenant domain}
                            {--create-db : Automatically create the database}
                            {--migrate : Run migrations after creating}
                            {--seed : Run seeders after migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantModel = config('tenant.tenant_model');
        $domainModel = 'App\\Models\\Domain';
        $connection = config('tenant.connections.central', 'central');

        $name = $this->argument('name');
        $slug = $this->option('slug') ?: Str::slug($name);
        $domain = $this->option('domain') ?: $slug;
        $databaseName = 'tenant_'.$slug;

        // Check if tenant already exists
        if ($tenantModel::on($connection)->where('slug', $slug)->exists()) {
            $this->error("Tenant with slug '{$slug}' already exists.");

            return self::FAILURE;
        }

        $this->info("Creating tenant: {$name}");
        $this->line("Slug: {$slug}");
        $this->line("Domain: {$domain}");
        $this->line("Database: {$databaseName}");
        $this->newLine();

        try {
            // Create database if requested
            if ($this->option('create-db')) {
                $this->info('Creating database...');
                DB::connection($connection)->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
                $this->info("✓ Database created: {$databaseName}");
            }

            // Create tenant
            $tenant = $tenantModel::on($connection)->create([
                'name' => $name,
                'slug' => $slug,
                'database_name' => $databaseName,
                'is_active' => true,
            ]);

            $this->info("✓ Tenant created with ID: {$tenant->getTenantKey()}");

            // Create domain
            if (class_exists($domainModel)) {
                $domainModel::on($connection)->create([
                    'tenant_id' => $tenant->getTenantKey(),
                    'domain' => $domain,
                    'is_primary' => true,
                ]);
                $this->info("✓ Domain created: {$domain}");
            }

            // Run migrations if requested
            if ($this->option('migrate')) {
                $this->newLine();
                $this->call('tenant:migrate', [
                    '--tenant' => $slug,
                    '--seed' => $this->option('seed'),
                ]);
            }

            $this->newLine();
            $this->info('Tenant created successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to create tenant: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
