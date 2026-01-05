<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \UendelSilveira\TenantCore\Contracts\TenantContract|null current()
 * @method static string|int|null key()
 * @method static string|null slug()
 * @method static bool isCentral()
 * @method static bool isTenant()
 *
 * @see \UendelSilveira\TenantCore\TenantManager
 */
class Tenant extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tenant';
    }
}
