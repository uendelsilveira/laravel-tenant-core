<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register tenant-specific routes for your application.
| These routes require a valid tenant context and will be loaded by the
| TenantServiceProvider within the "tenant" middleware group.
|
*/

Route::middleware('tenant')->group(function () {
    Route::get('/dashboard', function () {
        return view('tenant.dashboard', [
            'tenant' => tenant_current(),
        ]);
    })->name('tenant.dashboard');

    // Add your tenant routes here
});
