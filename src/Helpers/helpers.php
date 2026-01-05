<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	
	
	use UendelSilveira\TenantCore\Facades\Tenant;
	
	if (!function_exists('tenant_current')) {
		function tenant_current()
		{
			return Tenant::current();
		}
	}
	
	if (!function_exists('tenant_key')) {
		function tenant_key()
		{
			return Tenant::key();
		}
	}
	
	if (!function_exists('tenant_slug')) {
		function tenant_slug()
		{
			return Tenant::slug();
		}
	}
	
	if (!function_exists('tenant_is_central')) {
		function tenant_is_central(): bool
		{
			return Tenant::isCentral();
		}
	}
	
	if (!function_exists('tenant_is_tenant')) {
		function tenant_is_tenant(): bool
		{
			return Tenant::isTenant();
		}
	}
