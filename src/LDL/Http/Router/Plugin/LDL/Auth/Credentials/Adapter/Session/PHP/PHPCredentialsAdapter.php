<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\Session\PHP;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\CredentialsAdapterInterface;

class PHPCredentialsAdapter implements CredentialsAdapterInterface
{
    public function __construct()
    {

    }

    public function isAuthenticated(...$args): ?array
    {
        // TODO: Implement getCredentials() method.
    }
}