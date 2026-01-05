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

## 📦 Installation

Requires PHP 8.2+ and Laravel 10.0+ or 11.0+.

```bash
composer require uendelsilveira/laravel-tenant-core
```

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=tenant-config
```

This will create a `config/tenant.php` file.

### Environment Variables

Copy the variables from [.tenant-example.env](.tenant-example.env) to your `.env` file:

```env
# Domain
TENANT_CENTRAL_DOMAIN=example.com

# Database Connections
TENANT_CONNECTION_CENTRAL=central
TENANT_CONNECTION_TENANT=tenant

# Resolver (subdomain, path, header)
TENANT_RESOLVER=subdomain
TENANT_HEADER_NAME=X-Tenant-ID
TENANT_HEADER_SLUG_NAME=X-Tenant-Slug

# Cache
TENANT_CACHE_ENABLED=true
TENANT_CACHE_TTL=3600
TENANT_CACHE_STORE=
```

Ensure you have configured your database connections in `config/database.php`.

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

// Check if context is tenant
if (is_tenant()) {
    // In tenant context
}

// Check if context is central
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

**Optional: Custom Database Credentials**

If each tenant has its own database credentials, implement `TenantDatabaseCredentialsContract`:

```php
use UendelSilveira\TenantCore\Contracts\TenantDatabaseCredentialsContract;

class Tenant extends Model implements TenantContract, TenantDatabaseCredentialsContract
{
    // ... TenantContract methods ...

    public function getTenantDatabaseHost(): ?string
    {
        return $this->database_host;
    }

    public function getTenantDatabasePort(): ?int
    {
        return $this->database_port;
    }

    public function getTenantDatabaseUsername(): ?string
    {
        return $this->database_username;
    }

    public function getTenantDatabasePassword(): ?string
    {
        return $this->database_password;
    }
}
```

### Events

The package dispatches the following events during the tenant lifecycle:

- `TenantResolved` - When a tenant is identified from the request
- `TenantBooted` - When the tenant database connection is established
- `TenantEnded` - When the request completes and tenant context is cleared

```php
// In your EventServiceProvider
protected $listen = [
    \UendelSilveira\TenantCore\Events\TenantResolved::class => [
        \App\Listeners\LogTenantAccess::class,
    ],
    \UendelSilveira\TenantCore\Events\TenantBooted::class => [
        \App\Listeners\SetupTenantResources::class,
    ],
];
```

## 🏗 Architecture & Boundaries

This package strictly follows the principle that **infrastructure should not contain application rules**.

**What is included:**
- Tenant resolution (subdomain, path, header)
- Database connection switching
- Execution context management
- Lifecycle events

**What is NOT included:**
- Authentication / Authorization
- CRUD / Controllers
- UI / Views
- Billing / Plans

For a detailed explanation of the architectural boundaries, please refer to [docs/architecture-boundaries.md](docs/architecture-boundaries.md).

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 👨‍💻 Autor

**Uendel Silveira**
* 📧 [uendel.silveira@gmail.com](mailto:uendel.silveira@gmail.com)
* 🔗 [LinkedIn](https://linkedin.com/in/uendelsilveira)
* 🐙 [GitHub](https://github.com/uendelsilveira)