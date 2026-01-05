# Laravel Tenant Core

Infrastructure-only multi-tenant multi-database package for Laravel.

This package provides the essential infrastructure to build multi-tenant applications using a multi-database approach (one database per tenant). It focuses strictly on tenant resolution, database connection switching, and context management, leaving application-specific logic (authentication, authorization, UI, business rules) to the consuming application.

## 🚀 Features

- **Multi-Database Architecture**: Complete data isolation with one database per tenant.
- **Subdomain Resolution**: Automatically identifies tenants based on subdomains.
- **Context Management**: Global access to the current tenant context via Facade and Helper.
- **Middleware Integration**: Ready-to-use middleware for tenant identification and database initialization.
- **Event Driven**: Dispatches events during the tenant lifecycle (Resolved, Booted, Ended).
- **Agnostic**: No built-in authentication, UI, or business logic. You build your app your way.

## 📦 Installation

Requires PHP 8.5+ and Laravel 10.0+ or 11.0+.

```bash
composer require uendelsilveira/laravel-tenant-core
```

## ⚙️ Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="UendelSilveira\TenantCore\Providers\TenantServiceProvider"
```

This will create a `config/tenant.php` file. Configure your central domain and tenant model:

```php
return [
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'yourdomain.com'),
    
    'tenant_model' => App\Models\Tenant::class,
    
    'connections' => [
        'central' => 'central',
        'tenant' => 'tenant',
    ],
    
    'resolver' => [
        'type' => 'subdomain',
    ],
];
```

Ensure you have configured your database connections in `config/database.php`. You need at least one connection for the central database and a template connection for tenants.

## 🛠 Usage

### Middleware

Register the middleware in your `app/Http/Kernel.php` (Laravel 10) or `bootstrap/app.php` (Laravel 11) and superiors.

**For Tenant Routes:**
Apply the `IdentifyTenant` middleware to routes that should be accessible only to tenants.

```php
use UendelSilveira\TenantCore\Middleware\IdentifyTenant;

Route::middleware([IdentifyTenant::class])->group(function () {
    // Your tenant routes here
});
```

### Helpers & Facade

Access the current tenant anywhere in your application:

```php
use UendelSilveira\TenantCore\Facades\Tenant;

// Get current tenant
$tenant = tenant_current();

// Check if context is tenant
if (tenant_is_tenant()) {
    // ...
}

// Check if context is central
if (tenant_is_central()) {
    // ...
}
```

### Tenant Model

Your Tenant model should implement the `TenantContract`.

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

    public function getTenantName(): string
    {
        return $this->name;
    }

    public function getTenantDomain(): string
    {
        return $this->domain;
    }

    public function getTenantDatabaseName(): string
    {
        return $this->database_name;
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

## 🏗 Architecture & Boundaries

This package strictly follows the principle that **infrastructure should not contain application rules**.

**What is included:**
- Tenant resolution
- Database connection switching
- Execution context

**What is NOT included:**
- Authentication / Authorization
- CRUD / Controllers
- UI / Views
- Billing / Plans

For a detailed explanation of the architectural boundaries, please refer to [docs/architecture-boundaries.md](docs/architecture-boundaries.md).

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
