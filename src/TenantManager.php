<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore;

use UendelSilveira\TenantCore\Contracts\TenantContextContract;
use UendelSilveira\TenantCore\Contracts\TenantContract;

class TenantManager
{
    public function __construct(
        protected TenantContextContract $context
    ) {}

    public function current(): ?TenantContract
    {
        return $this->context->get();
    }

    public function key(): string|int|null
    {
        return $this->current()?->getTenantKey();
    }

    public function slug(): ?string
    {
        return $this->current()?->getTenantSlug();
    }

    public function isCentral(): bool
    {
        return $this->context->isCentral();
    }

    public function isTenant(): bool
    {
        return ! $this->isCentral();
    }
}
