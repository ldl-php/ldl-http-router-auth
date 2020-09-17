<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\Database\PDO\MySQL;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\CredentialsAdapterInterface;

class MySQLCredentialsAdapter implements CredentialsAdapterInterface
{
    public function __construct()
    {

    }

    public function isAuthenticated(...$args): ?array
    {
        // TODO: Implement getCredentials() method.
    }
}