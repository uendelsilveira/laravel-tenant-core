<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class Tenant extends Model implements TenantContract
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'central';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'database_name',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant's primary key.
     */
    public function getTenantKey(): string|int
    {
        return $this->id;
    }

    /**
     * Get the tenant's slug (URL-safe identifier).
     */
    public function getTenantSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the tenant's database name.
     */
    public function getTenantDatabase(): string
    {
        return $this->database_name;
    }

    /**
     * Get the tenant's domain/subdomain.
     * Returns the primary domain if available, otherwise the first domain.
     */
    public function getTenantDomain(): ?string
    {
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

    /**
     * Get the primary domain for the tenant.
     */
    public function primaryDomain(): ?Domain
    {
        return $this->domains()->where('is_primary', true)->first();
    }
}
