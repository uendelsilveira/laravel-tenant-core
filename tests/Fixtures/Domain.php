<?php

/*
| By Uendel Silveira
| Developer Web
| IDE: PhpStorm
| Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    /**
     * Get the tenant that owns the domain.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
