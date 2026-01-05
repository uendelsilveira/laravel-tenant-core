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

> ⚠️ **Important:** Move Laravel's default migrations to `database/migrations/central/` as they use the central database. See [Installation Guide](docs/INSTALLATION.md) for details.

**For detailed installation instructions, see [docs/INSTALLATION.md](docs/INSTALLATION.md)**

## Artisan Commands

The package includes powerful commands to manage tenants:

```bash
php artisan tenant:list              # List all tenants
php artisan tenant:create "Company"  # Create new tenant
php artisan tenant:migrate           # Run migrations for all tenants
php artisan tenant:migrate:fresh     # Fresh migrations
php artisan tenant:migrate:rollback  # Rollback migrations
php artisan tenant:seed              # Seed tenant databases
php artisan tenant:run {command}     # Run any Artisan command for tenants
```

See [Installation Guide](docs/INSTALLATION.md) for detailed command usage.

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
