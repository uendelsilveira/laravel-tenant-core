<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Exceptions;

use UendelSilveira\TenantCore\Contracts\TenantContract;

class TenantDatabaseException extends TenantException
{
    public function __construct(
        string $message = "Failed to connect to tenant database.",
        public readonly ?TenantContract $tenant = null,
        int $code = 500,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
