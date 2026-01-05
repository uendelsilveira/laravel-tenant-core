<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Exceptions;

class InvalidResolverException extends TenantException
{
    public function __construct(string $type, int $code = 500, ?\Throwable $previous = null)
    {
        $message = "Invalid or unsupported resolver type: {$type}";
        parent::__construct($message, $code, $previous);
    }
}
