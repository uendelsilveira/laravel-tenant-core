<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
|
| Here you can register central (non-tenant) routes for your application.
| These routes run on the central domain and will be loaded by the
| TenantServiceProvider within the "central" middleware group.
|
*/

Route::middleware('central')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
