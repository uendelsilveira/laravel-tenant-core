# Instruction.md

This file provides guidance to contributors when working with code in this repository.

## Package Overview

This is a Laravel package providing multi-tenant multi-database infrastructure. It is **infrastructure-only** - it provides tenant resolution, database switching, and context management, but intentionally excludes authentication, authorization, CRUD, UI, and business logic. See `docs/architecture-boundaries.md` for the full scope.

## Common Commands

### Testing
```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage

# Run specific test suite
vendor/bin/phpunit --testsuite=Unit
vendor/bin/phpunit --testsuite=Feature
```

### Code Style
```bash
# Format code (applies Laravel Pint rules)
composer format

# Check code style without making changes
composer check-style
```

### Package Development
```bash
# Publish config to test application
php artisan vendor:publish --tag=tenant-config

# Install dependencies
composer install

# Update dependencies
composer update
```

## Architecture

### Core Components

**Context Management** (`src/Context/`)
- `TenantContext`: Singleton that holds the current tenant instance
- Cleared automatically between Octane requests
- Accessed via `Tenant` facade or helper functions

**Tenant Resolution** (`src/Resolvers/`)
- `ResolverFactory`: Creates resolver instances based on config
- `SubdomainResolver`: Identifies tenant from subdomain (e.g., `acme.example.com`)
- `PathResolver`: Identifies tenant from URL path (e.g., `example.com/acme`)
- `HeaderResolver`: Identifies tenant from HTTP header (e.g., `X-Tenant-ID`)
- Each resolver queries the central database and supports optional caching

**Database Management** (`src/Database/`)
- `TenantDatabaseManager`: Handles dynamic connection switching
- Switches the `tenant` connection to point to tenant-specific database
- Supports per-tenant database credentials via `TenantDatabaseCredentialsContract`

**Middleware Chain** (`src/Middleware/`)
- `IdentifyTenant`: Resolves tenant from request using configured resolver
- `InitializeTenantDatabase`: Switches database connection to tenant's database
- `EnsureTenant`: Returns 404 if no tenant found (tenant routes)
- `EnsureCentral`: Returns 403 if tenant exists (central-only routes)

### Middleware Groups

The package registers two pre-configured middleware groups:
- `tenant`: Full chain for tenant routes (identify → database → ensure)
- `central`: Chain for central routes (identify → ensure central)

### Events

Lifecycle events dispatched during tenant operations:
- `TenantResolved`: When tenant is identified
- `TenantBooted`: When tenant database connection is established
- `TenantEnded`: When tenant context is cleared

### Configuration

All configuration is in `config/tenant.php`:
- `tenant_model`: The Eloquent model implementing `TenantContract`
- `connections.central`: Central database connection name
- `connections.tenant`: Tenant database connection name (template)
- `resolver.type`: Which resolver to use (subdomain/path/header)
- `cache.*`: Tenant lookup caching configuration

Models implementing `TenantContract` must provide:
- `getTenantKey()`: Primary identifier (usually ID)
- `getTenantSlug()`: URL-safe identifier
- `getTenantDatabase()`: Database name for this tenant
- `getTenantDomain()`: Domain/subdomain for this tenant

### Helper Functions

Global helpers available after package installation:
- `tenant_current()`: Get current tenant model instance
- `tenant_key()`: Get current tenant's key
- `tenant_slug()`: Get current tenant's slug
- `is_tenant()`: Check if in tenant context
- `is_central()`: Check if in central context

### PSR-4 Autoloading

Namespace: `UendelSilveira\TenantCore\`
Base directory: `src/`
Helper functions are autoloaded via `src/Helpers/helpers.php`

## Important Patterns

### Extending Resolvers

To create a custom resolver:
1. Implement `TenantResolverContract`
2. Add to `tenant.resolver.drivers` config array
3. Set `TENANT_RESOLVER` environment variable

### Testing Tenant Code

Tests use Orchestra Testbench for Laravel package testing:
- Test base class: `tests/TestCase.php`
- Unit tests: `tests/Unit/`
- Feature tests: `tests/Feature/`
- Fixtures: `tests/Fixtures/` (contains stub models/migrations)

### Database Connections

Always use explicit connections:
- Central models: `protected $connection = 'central';`
- Tenant models: Use default connection or omit (will be switched by middleware)
- Migrations: Specify connection in migration class or via `--database` flag

## Code Style

This package follows Laravel's coding style enforced by Pint with custom rules:
- Laravel preset with simplified null returns
- Single-line closures allowed in braces
- Named classes require braces, anonymous classes don't

Run `composer format` before committing code changes.
