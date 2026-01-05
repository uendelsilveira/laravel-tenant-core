<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/

namespace Uendel\LaravelTenantCore\Facades;

use Illuminate\Support\Facades\Facade;

class Tenant extends Facade
{
    protected static function getFacadeAccessor(): string { return 'tenant'; }
}
