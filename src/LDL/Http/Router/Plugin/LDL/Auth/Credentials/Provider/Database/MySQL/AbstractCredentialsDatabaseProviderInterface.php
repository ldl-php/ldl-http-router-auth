<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\MySQL;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;

interface AbstractCredentialsDatabaseProviderInterface extends CredentialsProviderInterface
{
    /**
     * Obtains the credentials connection
     * @return \PDO
     */
    public function getConnection(): \PDO;
}