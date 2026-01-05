<?php

/*
 By Uendel Silveira
 Developer Web
 IDE: PhpStorm
 Created at: 05/01/26
*/

namespace UendelSilveira\TenantCore\Resolvers;

use UendelSilveira\TenantCore\Contracts\TenantResolverContract;
use UendelSilveira\TenantCore\Exceptions\InvalidResolverException;

class ResolverFactory
{
    /**
     * Create a resolver instance based on configuration.
     *
     * @throws InvalidResolverException
     */
    public static function make(?string $type = null): TenantResolverContract
    {
        $type = $type ?? config('tenant.resolver.type', 'subdomain');

        $drivers = config('tenant.resolver.drivers', [
            'subdomain' => SubdomainResolver::class,
            'path' => PathResolver::class,
            'header' => HeaderResolver::class,
        ]);

        if (! isset($drivers[$type])) {
            throw new InvalidResolverException($type);
        }

        $resolverClass = $drivers[$type];

        if (! class_exists($resolverClass)) {
            throw new InvalidResolverException($type);
        }

        $resolver = app($resolverClass);

        if (! $resolver instanceof TenantResolverContract) {
            throw new InvalidResolverException(
                "{$resolverClass} must implement TenantResolverContract"
            );
        }

        return $resolver;
    }
}
