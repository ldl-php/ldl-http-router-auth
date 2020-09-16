<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\MySQL;

abstract class AbstractCredentialsDatabaseProvider implements AbstractCredentialsDatabaseProviderInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->$pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }
}