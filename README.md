# Laravel Tenant Core

Infrastructure-only multi-tenant package for Laravel using a multi-database approach.

## Features

- Multi-database architecture (one database per tenant)
- Multiple tenant resolvers (subdomain, path, header)
- Middleware groups for tenant and central routes
- Event-driven lifecycle (TenantResolved, TenantBooted, TenantEnded)
- Built-in caching and Laravel Octane support
- Infrastructure only - no authentication, authorization, or UI

## Requirements

- PHP 8.2+
- Laravel 10.x, 11.x, or 12.x
- MySQL, PostgreSQL, or SQLite

## Installation

```bash
composer require uendelsilveira/laravel-tenant-core
```

**Publish all assets:**

```bash
php artisan vendor:publish --provider="UendelSilveira\\TenantCore\\Providers\\TenantServiceProvider"
```

This publishes:
- Configuration (`config/tenant.php`)
- Migrations (central and tenant)
- Models (`Tenant`, `Domain`, `SystemUser`)
- Routes (`routes/tenant.php`, `routes/central.php`)
- Seeders (example tenant and SuperAdmin)

**Configure your `.env`:**

```env
DB_DATABASE_CENTRAL=central

TENANT_CENTRAL_DOMAIN=localhost
TENANT_CONNECTION_CENTRAL=central
TENANT_CONNECTION_TENANT=tenant
TENANT_RESOLVER=subdomain
```

**For detailed installation instructions, see [docs/INSTALLATION.md](docs/INSTALLATION.md)**

## Usage

**Tenant Routes:**

```php
Route::middleware('tenant')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

**Central Routes:**

```php
Route::middleware('central')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
```

**Helpers:**

```php
$tenant = tenant_current();  // Get current tenant
$id = tenant_key();          // Get tenant ID
$slug = tenant_slug();       // Get tenant slug

is_tenant();   // Check if in tenant context
is_central();  // Check if in central context
```

**Events:**

- `TenantResolved` - Tenant identified
- `TenantBooted` - Database connected
- `TenantEnded` - Context cleared

## Testing

```bash
composer test
```

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.

## Author

**Uendel Silveira** - [uendelsilveira@gmail.com](mailto:uendelsilveira@gmail.com)

[LinkedIn](https://linkedin.com/in/uendelsilveira) • [GitHub](https://github.com/uendelsilveira)
