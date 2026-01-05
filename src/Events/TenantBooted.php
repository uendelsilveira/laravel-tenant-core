<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class TenantBooted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param TenantContract $tenant
     */
    public function __construct(
        public TenantContract $tenant
    ) {}
}

