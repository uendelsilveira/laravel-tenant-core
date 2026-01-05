# Guia de Instalação - Laravel Tenant Core

Este guia apresenta o passo a passo completo para instalar e configurar o pacote `laravel-tenant-core` em uma aplicação Laravel.

## Índice

1. [Requisitos](#requisitos)
2. [Instalação](#instalação)
3. [Configuração do Banco de Dados](#configuração-do-banco-de-dados)
4. [Configuração do Pacote](#configuração-do-pacote)
5. [Criando o Model Tenant](#criando-o-model-tenant)
6. [Criando a Migration](#criando-a-migration)
7. [Configurando as Rotas](#configurando-as-rotas)
8. [Testando a Instalação](#testando-a-instalação)
9. [Próximos Passos](#próximos-passos)

---

## Requisitos

- PHP 8.2 ou superior
- Laravel 10.x, 11.x ou 12.x
- Banco de dados (MySQL, PostgreSQL, SQLite)

---

## Instalação

### 1. Instalar o pacote via Composer

```bash
composer require uendelsilveira/laravel-tenant-core
```

### 2. Publicar o arquivo de configuração

```bash
php artisan vendor:publish --tag=tenant-config
```

Isso criará o arquivo `config/tenant.php`.

---

## Configuração do Banco de Dados

### 3. Configurar as conexões no `config/database.php`

Você precisa de pelo menos duas conexões: uma para o banco **central** e outra para os bancos dos **tenants**.

```php
// config/database.php

'connections' => [

    // Conexão central (onde ficam os dados dos tenants)
    'central' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE_CENTRAL', 'central'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],

    // Conexão tenant (template - o database será alterado dinamicamente)
    'tenant' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => null, // Será preenchido dinamicamente
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],

],
```

### 4. Adicionar variáveis de ambiente no `.env`

```env
# Banco de dados central
DB_DATABASE_CENTRAL=central

# Configurações do Tenant Core
TENANT_CENTRAL_DOMAIN=seudominio.com
TENANT_CONNECTION_CENTRAL=central
TENANT_CONNECTION_TENANT=tenant
TENANT_RESOLVER=subdomain
TENANT_CACHE_ENABLED=true
TENANT_CACHE_TTL=3600
```

> 💡 **Dica:** Copie as variáveis do arquivo `.tenant-example.env` incluído no pacote.

---

## Configuração do Pacote

### 5. Revisar o arquivo `config/tenant.php`

```php
// config/tenant.php

return [
    // Domínio central da aplicação
    'central_domain' => env('TENANT_CENTRAL_DOMAIN', 'seudominio.com'),

    // Model que representa o tenant
    'tenant_model' => App\Models\Tenant::class,

    // Conexões de banco de dados
    'connections' => [
        'central' => env('TENANT_CONNECTION_CENTRAL', 'central'),
        'tenant' => env('TENANT_CONNECTION_TENANT', 'tenant'),
    ],

    // Configuração do resolver
    'resolver' => [
        'type' => env('TENANT_RESOLVER', 'subdomain'),
        // ...
    ],

    // Cache
    'cache' => [
        'enabled' => env('TENANT_CACHE_ENABLED', true),
        'ttl' => env('TENANT_CACHE_TTL', 3600),
        // ...
    ],
];
```

---

## Criando o Model Tenant

### 6. Criar o Model `Tenant`

```bash
php artisan make:model Tenant
```

### 7. Implementar o contrato `TenantContract`

```php
<?php
// app/Models/Tenant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class Tenant extends Model implements TenantContract
{
    protected $connection = 'central'; // Importante!

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database_name',
    ];

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

### 8. (Opcional) Credenciais de banco por tenant

Se cada tenant tiver credenciais próprias de banco de dados:

```php
<?php
// app/Models/Tenant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use UendelSilveira\TenantCore\Contracts\TenantContract;
use UendelSilveira\TenantCore\Contracts\TenantDatabaseCredentialsContract;

class Tenant extends Model implements TenantContract, TenantDatabaseCredentialsContract
{
    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database_name',
        'database_host',
        'database_port',
        'database_username',
        'database_password',
    ];

    protected $hidden = [
        'database_password',
    ];

    // Métodos do TenantContract...

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

---

## Criando a Migration

### 9. Criar a migration para a tabela `tenants`

```bash
php artisan make:migration create_tenants_table --path=database/migrations/central
```

```php
<?php
// database/migrations/central/xxxx_xx_xx_create_tenants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'central';

    public function up(): void
    {
        Schema::connection('central')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->unique();
            $table->string('database_name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('tenants');
    }
};
```

### 10. Executar a migration

```bash
php artisan migrate --database=central --path=database/migrations/central
```

---

## Configurando as Rotas

### 11. Configurar rotas de tenant

```php
<?php
// routes/tenant.php

use Illuminate\Support\Facades\Route;

// Rotas que requerem contexto de tenant
Route::middleware('tenant')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('tenant.dashboard', [
            'tenant' => tenant_current(),
        ]);
    })->name('tenant.dashboard');

    Route::get('/profile', function () {
        return 'Tenant: ' . tenant_slug();
    })->name('tenant.profile');

});
```

### 12. Configurar rotas centrais

```php
<?php
// routes/central.php

use Illuminate\Support\Facades\Route;

// Rotas do domínio central (admin, landing page, etc)
Route::middleware('central')->group(function () {
    
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});
```

### 13. Registrar os arquivos de rotas

**Laravel 11+** (`bootstrap/app.php`):

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/tenant.php'));
            
            Route::middleware('web')
                ->group(base_path('routes/central.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

**Laravel 10** (`app/Providers/RouteServiceProvider.php`):

```php
public function boot(): void
{
    $this->routes(function () {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        Route::middleware('web')
            ->group(base_path('routes/tenant.php'));

        Route::middleware('web')
            ->group(base_path('routes/central.php'));
    });
}
```

---

## Testando a Instalação

### 14. Criar um tenant de teste

```bash
php artisan tinker
```

```php
use App\Models\Tenant;

Tenant::create([
    'name' => 'Empresa Teste',
    'slug' => 'teste',
    'domain' => 'teste',
    'database_name' => 'tenant_teste',
]);
```

### 15. Criar o banco de dados do tenant

```sql
CREATE DATABASE tenant_teste;
```

### 16. Configurar hosts locais (desenvolvimento)

Adicione ao arquivo `/etc/hosts` (Linux/Mac) ou `C:\Windows\System32\drivers\etc\hosts` (Windows):

```
127.0.0.1 seudominio.test
127.0.0.1 teste.seudominio.test
```

### 17. Testar o acesso

```bash
# Iniciar servidor
php artisan serve --host=seudominio.test --port=8000
```

Acesse:
- **Central:** `http://seudominio.test:8000`
- **Tenant:** `http://teste.seudominio.test:8000/dashboard`

### 18. Verificar se está funcionando

Crie uma rota de debug:

```php
Route::get('/debug', function () {
    return [
        'is_tenant' => is_tenant(),
        'is_central' => is_central(),
        'tenant_key' => tenant_key(),
        'tenant_slug' => tenant_slug(),
        'tenant' => tenant_current()?->toArray(),
    ];
});
```

---

## Próximos Passos

Após a instalação básica, você pode:

### Listeners de Eventos

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    \UendelSilveira\TenantCore\Events\TenantResolved::class => [
        \App\Listeners\LogTenantAccess::class,
    ],
    \UendelSilveira\TenantCore\Events\TenantBooted::class => [
        \App\Listeners\SetupTenantResources::class,
    ],
    \UendelSilveira\TenantCore\Events\TenantEnded::class => [
        \App\Listeners\CleanupTenantResources::class,
    ],
];
```

### Migrations por Tenant

Crie um comando para rodar migrations em todos os tenants:

```php
// app/Console/Commands/TenantMigrate.php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantMigrate extends Command
{
    protected $signature = 'tenant:migrate {--tenant=}';
    protected $description = 'Run migrations for tenants';

    public function handle(): void
    {
        $tenants = $this->option('tenant')
            ? Tenant::where('slug', $this->option('tenant'))->get()
            : Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant: {$tenant->slug}");

            config(['database.connections.tenant.database' => $tenant->database_name]);

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            $this->info(Artisan::output());
        }
    }
}
```

### Usar diferentes Resolvers

**Path Resolver:**
```env
TENANT_RESOLVER=path
```
Acesso: `http://seudominio.com/teste/dashboard`

**Header Resolver:**
```env
TENANT_RESOLVER=header
TENANT_HEADER_NAME=X-Tenant-ID
```
Acesso via API com header: `X-Tenant-ID: 1`

---

## Solução de Problemas

### Tenant não encontrado

- Verifique se o domínio/slug existe na tabela `tenants`
- Verifique se `TENANT_CENTRAL_DOMAIN` está correto
- Limpe o cache: `php artisan cache:clear`

### Erro de conexão com banco

- Verifique se o banco do tenant existe
- Verifique as credenciais de acesso
- Verifique se a conexão `tenant` está configurada corretamente

### Middleware não funciona

- Verifique se está usando o middleware group correto (`tenant` ou `central`)
- Verifique a ordem dos middlewares

---

## Suporte

- **Issues:** [GitHub Issues](https://github.com/uendelsilveira/laravel-tenant-core/issues)
- **Documentação:** [README.md](../README.md)
- **Limites Arquiteturais:** [architecture-boundaries.md](architecture-boundaries.md)
