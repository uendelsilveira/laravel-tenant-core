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
		public function getTenantKey(): string|int;
		
		public function getTenantSlug(): string;
		
		public function getTenantDatabase(): string;
	}