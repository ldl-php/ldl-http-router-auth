<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;

interface AbstractCredentialsPDOProviderInterface extends CredentialsProviderInterface
{
    /**
     * Obtains the credentials connection
     * @return \PDO
     */
    public function getConnection(): \PDO;
}