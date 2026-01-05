<?php
	/*
	 By Uendel Silveira
	 Developer Web
	 IDE: PhpStorm
	 Created at: 05/01/26
	*/
	return [
		
		'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'dominio.com'),
		
		'tenant_model' => App\Models\Tenant::class,
		
		'connections' => [
			'central' => 'central',
			'tenant' => 'tenant',
		],
		
		'resolver' => [
			'type' => 'subdomain',
		],
	
	];