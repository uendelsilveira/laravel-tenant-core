<?php
/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Resolvers;

use InvalidArgumentException;
use UendelSilveira\TenantCore\Contracts\TenantResolverContract;

class ResolverFactory
{
    /**
     * Create a resolver instance based on configuration.
     *
     * @param string|null $type
     * @return TenantResolverContract
     * @throws InvalidArgumentException
     */
    public static function make(?string $type = null): TenantResolverContract
    {
        $type = $type ?? config('tenant.resolver.type', 'subdomain');
        
        return match ($type) {
            'subdomain' => new SubdomainResolver(),
            'path' => new PathResolver(),
            'header' => new HeaderResolver(),
            default => throw new InvalidArgumentException("Unsupported resolver type: {$type}"),
        };
    }
}

