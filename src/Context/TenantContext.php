<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	
	namespace UendelSilveira\TenantCore\Context;

	use UendelSilveira\TenantCore\Contracts\TenantContextContract;
	use UendelSilveira\TenantCore\Contracts\TenantContract;
	
	class TenantContext implements TenantContextContract
	{
		protected ?TenantContract $tenant = null;
		
		public function get(): ?TenantContract
		{
			return $this->tenant;
		}
		
		public function set(?TenantContract $tenant): void
		{
			$this->tenant = $tenant;
		}
		
		public function clear(): void
		{
			$this->tenant = null;
		}
		
		public function isCentral(): bool
		{
			return $this->tenant === null;
		}
	}