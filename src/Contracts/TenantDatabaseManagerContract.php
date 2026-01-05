<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	
	namespace UendelSilveira\TenantCore\Contracts;
	
	interface TenantDatabaseManagerContract
	{
		public function connect(TenantContract $tenant): void;
		
		public function disconnect(): void;
	}