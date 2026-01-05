<?php

namespace UendelSilveira\TenantCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TenantRunCommand extends Command
{
    protected $signature = 'tenant:run
                            {artisan_command : The Artisan command to run}
                            {--tenant= : The tenant slug to run the command for}';

    protected $description = 'Run an Artisan command for one or all tenants';

    public function handle()
    {
        $command = $this->argument('artisan_command');
        $tenantSlug = $this->option('tenant');

        $tenantModel = config('tenant.tenant_model');
        $connection = config('tenant.connections.central', 'central');

        $tenants = $tenantSlug
            ? $tenantModel::on($connection)->where('slug', $tenantSlug)->get()
            : $tenantModel::on($connection)->where('is_active', true)->get();

        if ($tenants->isEmpty()) {
            $this->error('No tenants found.');
            return 1;
        }

        $this->info("Running command: {$command}");
        $this->newLine();

        $successCount = 0;
        $errorCount = 0;

        foreach ($tenants as $tenant) {
            $this->line("Running for tenant: <comment>{$tenant->name}</comment> (<info>{$tenant->slug}</info>)");

            try {
                // Switch to tenant database
                config([
                    'database.connections.tenant.database' => $tenant->getTenantDatabase(),
                ]);
                DB::purge('tenant');
                DB::reconnect('tenant');

                // Run the command
                $exitCode = Artisan::call($command, [], $this->getOutput());

                if ($exitCode === 0) {
                    $this->info("✓ Completed: {$tenant->slug}");
                    $successCount++;
                } else {
                    $this->error("✗ Failed: {$tenant->slug} (exit code: {$exitCode})");
                    $errorCount++;
                }

                // Show command output if any
                $output = Artisan::output();
                if (!empty(trim($output))) {
                    $this->line($output);
                }
            } catch (\Exception $e) {
                $this->error("✗ Error for {$tenant->slug}: " . $e->getMessage());
                $errorCount++;
            }

            $this->newLine();
        }

        // Summary
        $this->info("Summary:");
        $this->line("✓ Success: {$successCount}");
        if ($errorCount > 0) {
            $this->line("✗ Errors: {$errorCount}");
        }

        return $errorCount > 0 ? 1 : 0;
    }
}
