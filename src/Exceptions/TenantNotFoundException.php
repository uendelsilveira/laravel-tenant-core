<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Exceptions;

class TenantNotFoundException extends TenantException
{
    public function __construct(string $identifier = '', int $code = 404, ?\Throwable $previous = null)
    {
        $message = $identifier
            ? "Tenant not found: {$identifier}"
            : "Tenant not found.";

        parent::__construct($message, $code, $previous);
    }
}
