<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Contracts;

use Illuminate\Http\Request;

interface TenantResolverContract
{
    /**
     * Resolve the tenant from the given request.
     */
    public function resolve(Request $request): ?TenantContract;
}
