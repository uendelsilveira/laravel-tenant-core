<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Contracts;

interface TenantContract
{
    /**
     * Get the unique identifier of the tenant.
     */
    public function getTenantKey(): string|int;

    /**
     * Get the slug/identifier used in URLs.
     */
    public function getTenantSlug(): string;

    /**
     * Get the database name for this tenant.
     */
    public function getTenantDatabase(): string;

    /**
     * Get the domain/subdomain for this tenant.
     */
    public function getTenantDomain(): ?string;
}