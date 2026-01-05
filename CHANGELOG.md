# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-01-05

### Added
- Initial release
- Multi-database tenant architecture
- Tenant resolvers: Subdomain, Path, Header
- Tenant context management
- Database connection switching with validation
- Custom database credentials support via `TenantDatabaseCredentialsContract`
- Middleware groups: `tenant` and `central`
- Middleware aliases: `tenant.identify`, `tenant.database`, `tenant.ensure`, `tenant.central`
- Lifecycle events: `TenantResolved`, `TenantBooted`, `TenantEnded`
- Built-in tenant lookup caching
- Laravel Octane support
- Helper functions: `tenant()`, `tenant_current()`, `tenant_key()`, `tenant_slug()`, `is_central()`, `is_tenant()`
- Facade: `Tenant`
- Custom exceptions: `TenantException`, `TenantNotFoundException`, `TenantDatabaseException`, `InvalidResolverException`
- Comprehensive test suite
- Full documentation
