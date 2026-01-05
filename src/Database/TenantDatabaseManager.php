<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	
	use UendelSilveira\TenantCore\Contracts\TenantContract;
	use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
	
	class TenantDatabaseManager implements TenantDatabaseManagerContract
	{
		public function connect(TenantContract $tenant): void
		{
			config([
				'database.connections.tenant.database' => $tenant->getTenantDatabase(),
			]);
			
			DB::purge('tenant');
			DB::reconnect('tenant');
			
			DB::setDefaultConnection('tenant');
		}
		
		public function disconnect(): void
		{
			DB::setDefaultConnection(
				config('tenant.connections.central')
			);
		}
	}