<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	
	use Illuminate\Support\ServiceProvider;
	use UendelSilveira\TenantCore\Contracts\TenantContextContract;
	use UendelSilveira\TenantCore\Contracts\TenantDatabaseManagerContract;
	
	class TenantServiceProvider extends ServiceProvider
	{
		public function register(): void
		{
			$this->mergeConfigFrom(__DIR__.'/../config/tenant.php', 'tenant');
			
			$this->app->singleton(TenantContextContract::class, TenantContext::class);
			$this->app->singleton(TenantDatabaseManagerContract::class, TenantDatabaseManager::class);
		}
		
		public function boot(): void
		{
			$this->publishes([
				__DIR__.'../../config/tenant.php' => config_path('tenant.php'),
			], 'tenant-config');
		}
	}