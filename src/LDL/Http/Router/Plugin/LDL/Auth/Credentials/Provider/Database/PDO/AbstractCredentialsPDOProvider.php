<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO;

abstract class AbstractCredentialsPDOProvider implements AbstractCredentialsPDOProviderInterface
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }
}