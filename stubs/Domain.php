<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
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
        'tenant_id',
        'domain',
        'is_primary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the tenant that owns the domain.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
