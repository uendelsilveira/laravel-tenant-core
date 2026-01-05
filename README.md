# Laravel Tenant Core

Infrastructure-only multi-tenant multi-database package for Laravel.

This package provides the essential infrastructure to build multi-tenant applications using a multi-database approach (one database per tenant). It focuses strictly on tenant resolution, database connection switching, and context management, leaving application-specific logic (authentication, authorization, UI, business rules) to the consuming application.

## 🚀 Features

- **Multi-Database Architecture**: Complete data isolation with one database per tenant.
- **Multiple Resolvers**: Identify tenants via subdomain, path, or HTTP header.
- **Context Management**: Global access to the current tenant context via Facade and Helper.
- **Middleware Integration**: Ready-to-use middleware groups for tenant and central routes.
- **Event Driven**: Dispatches events during the tenant lifecycle (Resolved, Booted, Ended).
- **Caching**: Built-in tenant lookup caching for improved performance.
- **Laravel Octane Support**: Automatic context cleanup between requests.
- **Agnostic**: No built-in authentication, UI, or business logic. You build your app your way.

## 📖 Documentation

- **[Installation Guide](docs/INSTALLATION.md)** - Complete step-by-step installation tutorial
- **[Architecture Boundaries](docs/architecture-boundaries.md)** - What this package does and doesn't do
- **[Environment Variables](.tenant-example.env)** - All available configuration options

## 📦 Quick Installation

Requires PHP 8.2+ and Laravel 10.x, 11.x or 12.x.

```bash
composer require uendelsilveira/laravel-tenant-core
```

```bash
php artisan vendor:publish --tag=tenant-config
```

> 📘 For a complete installation guide with database setup, model creation, and route configuration, see **[docs/INSTALLATION.md](docs/INSTALLATION.md)**.

## ⚙️ Configuration

Copy the variables from [.tenant-example.env](.tenant-example.env) to your `.env` file:

```env
# Domain
TENANT_CENTRAL_DOMAIN=example.com

# Database Connections
TENANT_CONNECTION_CENTRAL=central
TENANT_CONNECTION_TENANT=tenant

# Resolver (subdomain, path, header)
TENANT_RESOLVER=subdomain

# Cache
TENANT_CACHE_ENABLED=true
TENANT_CACHE_TTL=3600
```

## 🛠 Usage

### Middleware Groups

The package registers two middleware groups automatically:

**For Tenant Routes** (requires tenant context):
```php
Route::middleware('tenant')->group(function () {
    Route::get('/dashboard', DashboardController::class);
});
```

**For Central Routes** (no tenant allowed):
```php
Route::middleware('central')->group(function () {
    Route::get('/admin', AdminController::class);
});
```

**Available Middleware Aliases:**
- `tenant.identify` - Identifies the tenant from the request
- `tenant.database` - Initializes the tenant database connection
- `tenant.ensure` - Ensures a tenant exists (404 if not)
- `tenant.central` - Ensures no tenant exists (403 if tenant present)

### Helpers & Facade

Access the current tenant anywhere in your application:

```php
use UendelSilveira\TenantCore\Facades\Tenant;

// Get current tenant
$tenant = tenant_current();
// or
$tenant = Tenant::current();

// Get tenant key
$id = tenant_key();

// Get tenant slug
$slug = tenant_slug();

// Check context
if (is_tenant()) {
    // In tenant context
}

if (is_central()) {
    // In central context
}
```

### Tenant Model

Your Tenant model must implement the `TenantContract` interface:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class Tenant extends Model implements TenantContract
{
    protected $connection = 'central';

    public function getTenantKey(): string|int
    {
        return $this->id;
    }

    public function getTenantSlug(): string
    {
        return $this->slug;
    }

    public function getTenantDatabase(): string
    {
        return $this->database_name;
    }

    public function getTenantDomain(): ?string
    {
        return $this->domain;
    }
}
```

### Events

The package dispatches lifecycle events:

- `TenantResolved` - When a tenant is identified
- `TenantBooted` - When tenant database is connected
- `TenantEnded` - When tenant context is cleared

```php
// EventServiceProvider
protected $listen = [
    \UendelSilveira\TenantCore\Events\TenantBooted::class => [
        \App\Listeners\SetupTenantResources::class,
    ],
];
```

## 🏗 Architecture

This package provides **infrastructure only**:

| ✅ Included | ❌ Not Included |
|-------------|-----------------|
| Tenant resolution | Authentication |
| Database switching | Authorization |
| Context management | CRUD / Controllers |
| Lifecycle events | UI / Views |
| Caching | Billing / Plans |

See [docs/architecture-boundaries.md](docs/architecture-boundaries.md) for details.

## 🧪 Testing

```bash
composer test
```

## 🤝 Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.

## 👨‍💻 Author

**Uendel Silveira**

[![Email](https://img.shields.io/badge/Email-uendelsilveira%40gmail.com-blue)](mailto:uendelsilveira@gmail.com)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-uendelsilveira-blue)](https://linkedin.com/in/uendelsilveira)
[![GitHub](https://img.shields.io/badge/GitHub-uendelsilveira-black)](https://github.com/uendelsilveira)