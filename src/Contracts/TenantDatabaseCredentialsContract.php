<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Contracts;

/**
 * Optional contract for tenants that have custom database credentials.
 * Implement this interface if each tenant has its own database user/password.
 */
interface TenantDatabaseCredentialsContract
{
    /**
     * Get the database host for this tenant.
     */
    public function getTenantDatabaseHost(): ?string;

    /**
     * Get the database port for this tenant.
     */
    public function getTenantDatabasePort(): ?int;

    /**
     * Get the database username for this tenant.
     */
    public function getTenantDatabaseUsername(): ?string;

    /**
     * Get the database password for this tenant.
     */
    public function getTenantDatabasePassword(): ?string;
}
