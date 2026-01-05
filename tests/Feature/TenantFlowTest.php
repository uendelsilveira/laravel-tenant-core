<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Events\TenantBooted;
use UendelSilveira\TenantCore\Events\TenantEnded;
use UendelSilveira\TenantCore\Events\TenantResolved;
use UendelSilveira\TenantCore\Middleware\IdentifyTenant;
use UendelSilveira\TenantCore\Middleware\InitializeTenantDatabase;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class TenantFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Create tenants table
        Schema::connection('central')->create('tenants', function ($table) {
            $table->id();
            $table->string('slug');
            $table->string('domain');
            $table->string('database_name');
        });

        // Create test tenant with :memory: database for SQLite
        Tenant::on('central')->create([
            'id' => 1,
            'slug' => 'acme',
            'domain' => 'acme',
            'database_name' => ':memory:'
        ]);
    }

    /** @test */
    public function it_completes_full_tenant_flow(): void
    {
        Event::fake();

        // Setup route with middlewares
        Route::middleware([IdentifyTenant::class, InitializeTenantDatabase::class])
            ->get('/test', function () {
                $context = app(TenantContextContract::class);
                $tenant = $context->get();

                return response()->json([
                    'tenant_id' => $tenant?->getTenantKey(),
                    'tenant_slug' => $tenant?->getTenantSlug(),
                    'connection' => DB::getDefaultConnection(),
                ]);
            });

        // Make request to tenant subdomain
        $response = $this->get('http://acme.example.com/test');

        $response->assertStatus(200);
        $response->assertJson([
            'tenant_id' => 1,
            'tenant_slug' => 'acme',
            'connection' => 'tenant',
        ]);

        // Verify events were dispatched
        Event::assertDispatched(TenantResolved::class);
        Event::assertDispatched(TenantBooted::class);
        Event::assertDispatched(TenantEnded::class);

        // After request, should be back to central
        $this->assertEquals('central', DB::getDefaultConnection());
    }

    /** @test */
    public function it_stays_on_central_for_central_domain(): void
    {
        Event::fake();

        // Setup route with middlewares
        Route::middleware([IdentifyTenant::class, InitializeTenantDatabase::class])
            ->get('/test', function () {
                $context = app(TenantContextContract::class);

                return response()->json([
                    'is_central' => $context->isCentral(),
                    'connection' => DB::getDefaultConnection(),
                ]);
            });

        // Make request to central domain
        $response = $this->get('http://example.com/test');

        $response->assertStatus(200);
        $response->assertJson([
            'is_central' => true,
            'connection' => 'central',
        ]);

        // Verify tenant events were NOT dispatched
        Event::assertNotDispatched(TenantResolved::class);
        Event::assertNotDispatched(TenantBooted::class);
        Event::assertNotDispatched(TenantEnded::class);
    }
}

