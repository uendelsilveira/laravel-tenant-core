<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created: 05/01/2026
*/

namespace UendelSilveira\TenantCore\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use UendelSilveira\TenantCore\Context\TenantContext;
use UendelSilveira\TenantCore\Tests\Fixtures\Tenant;
use UendelSilveira\TenantCore\Tests\TestCase;

class TenantContextTest extends TestCase
{
    protected TenantContext $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new TenantContext();
    }

    #[Test]
    public function it_starts_with_no_tenant(): void
    {
        $this->assertNull($this->context->get());
        $this->assertTrue($this->context->isCentral());
    }

    #[Test]
    public function it_can_set_a_tenant(): void
    {
        $tenant = new Tenant(['id' => 1, 'slug' => 'test', 'database_name' => 'test_db']);

        $this->context->set($tenant);

        $this->assertSame($tenant, $this->context->get());
        $this->assertFalse($this->context->isCentral());
    }

    #[Test]
    public function it_can_clear_tenant(): void
    {
        $tenant = new Tenant(['id' => 1, 'slug' => 'test', 'database_name' => 'test_db']);

        $this->context->set($tenant);
        $this->assertNotNull($this->context->get());

        $this->context->clear();

        $this->assertNull($this->context->get());
        $this->assertTrue($this->context->isCentral());
    }

    #[Test]
    public function it_can_set_tenant_to_null(): void
    {
        $tenant = new Tenant(['id' => 1, 'slug' => 'test', 'database_name' => 'test_db']);

        $this->context->set($tenant);
        $this->assertNotNull($this->context->get());

        $this->context->set(null);

        $this->assertNull($this->context->get());
        $this->assertTrue($this->context->isCentral());
    }
}
