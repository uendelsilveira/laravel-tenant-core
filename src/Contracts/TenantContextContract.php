<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	namespace UendelSilveira\TenantCore\Contracts;
	
	interface TenantContextContract
	{
		public function get(): ?TenantContract;
		
		public function set(?TenantContract $tenant): void;
		
		public function clear(): void;
		
		public function isCentral(): bool;
	}