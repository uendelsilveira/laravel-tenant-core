<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Database;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Contracts\TenantDatabaseCredentialsContract;
use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
use UendelSilveira\TenantCore\Exceptions\TenantDatabaseException;

class TenantDatabaseManager implements TenantDatabaseManagerContract
{
    /**
     * Connect to the tenant's database.
     *
     * @throws TenantDatabaseException
     */
    public function connect(TenantContract $tenant): void
    {
        $database = $tenant->getTenantDatabase();

        if (empty($database)) {
            throw new TenantDatabaseException(
                "Tenant '{$tenant->getTenantKey()}' has no database configured.",
                $tenant
            );
        }

        $connectionName = config('tenant.connections.tenant', 'tenant');

        // Set the database name
        Config::set("database.connections.{$connectionName}.database", $database);

        // Set optional credentials if tenant implements the credentials contract
        if ($tenant instanceof TenantDatabaseCredentialsContract) {
            $this->setCredentials($connectionName, $tenant);
        }

        // Purge and reconnect
        DB::purge($connectionName);

        try {
            DB::reconnect($connectionName);
            // Force actual connection to validate credentials
            DB::connection($connectionName)->getPdo();
        } catch (\Exception $e) {
            throw new TenantDatabaseException(
                "Failed to connect to tenant database '{$database}': {$e->getMessage()}",
                $tenant,
                500,
                $e
            );
        }

        DB::setDefaultConnection($connectionName);
    }

    /**
     * Disconnect from the tenant database and switch back to central.
     */
    public function disconnect(): void
    {
        $centralConnection = config('tenant.connections.central', 'central');
        DB::setDefaultConnection($centralConnection);
    }

    /**
     * Set custom database credentials for the tenant connection.
     */
    protected function setCredentials(string $connectionName, TenantDatabaseCredentialsContract $tenant): void
    {
        $credentials = [
            'host' => $tenant->getTenantDatabaseHost(),
            'port' => $tenant->getTenantDatabasePort(),
            'username' => $tenant->getTenantDatabaseUsername(),
            'password' => $tenant->getTenantDatabasePassword(),
        ];

        foreach ($credentials as $key => $value) {
            if ($value !== null) {
                Config::set("database.connections.{$connectionName}.{$key}", $value);
            }
        }
    }
}
