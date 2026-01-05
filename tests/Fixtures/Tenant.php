<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class Tenant extends Model implements TenantContract
{
    protected $guarded = [];

    public $timestamps = false;

    public function getTenantKey(): string|int
    {
        return $this->id;
    }

    public function getTenantSlug(): string
    {
        return $this->slug;
    }

    public function getTenantDatabase(): string
    {
        return $this->database_name;
    }

    public function getTenantDomain(): ?string
    {
        // For backward compatibility with tests that use 'domain' field
        if (isset($this->attributes['domain'])) {
            return $this->domain;
        }

        // New structure: get from domains relationship
        return $this->domains()->where('is_primary', true)->first()?->domain
            ?? $this->domains()->first()?->domain;
    }

    /**
     * Get the domains for the tenant.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }
}
