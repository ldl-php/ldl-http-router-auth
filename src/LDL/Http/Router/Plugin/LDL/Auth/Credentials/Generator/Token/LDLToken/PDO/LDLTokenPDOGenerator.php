<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\PDO;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\LDLTokenGeneratorOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator\RandomHashGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator\RandomHashGenerator;

class LDLTokenPDOGenerator implements TokenGeneratorInterface
{
    private const NAMESPACE = 'LDLAuthPlugin';
    private const NAME = 'LDLTokenPDOGenerator';

    private const DEFAULT_TABLE = 'ldl_auth_token';

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var LDLTokenGeneratorOptions
     */
    private $options;

    /**
     * @var RandomHashGeneratorInterface
     */
    private $hashGenerator;

    public function __construct(
        \PDO $connection,
        string $tableName = null,
        RandomHashGeneratorInterface $hashGenerator=null,
        LDLTokenGeneratorOptions $options = null
    )
    {
        $this->connection = $connection;
        $this->tableName = $tableName ?? self::DEFAULT_TABLE;
        $this->hashGenerator = $hashGenerator ?? new RandomHashGenerator();
        $this->options = $options ?? new LDLTokenGeneratorOptions('/auth/token/refresh');
    }

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function exists($token) : bool
    {
        $sql = sprintf(
            'SELECT `token` FROM `%s` WHERE `token`=:token',
            $this->tableName
        );

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            ':token' => $token
        ]);

        return (bool) $stmt->fetch(\PDO::FETCH_COLUMN);
    }

    public function create(
        array $user,
        ResponseInterface $response
    ) : string
    {
        $token = $this->hashGenerator->generate();
        $headers = $this->options->getHeaders();

        $sql = sprintf(
            'INSERT INTO `%s` SET user=:user, `token`=:token, `createdAt`=:createdAt, `expiresAt`=:expiresAt',
            $this->tableName
        );

        $stmt = $this->connection->prepare($sql);

        $utcTZ = new \DateTimeZone('UTC');
        $now = new \DateTime('now', $utcTZ);
        $expiresAt = $now->add($this->options->getExpiresAt());

        $stmt->execute([
            ':token' => $token,
            ':user' => $user['user'],
            ':createdAt' => $now->format('Y-m-d H:i:s'),
            ':expiresAt' => $expiresAt->format('Y-m-d H:i:s')
        ]);

        $response->getHeaderBag()->set($headers['token'], $token);
        $response->getHeaderBag()->set($headers['refresh'], $this->options->getRefreshEndpoint());
        $response->getHeaderBag()->set($headers['expiresAt'], $expiresAt->format(\DateTimeInterface::RFC3339_EXTENDED));

        return $token;
    }

    public function destroy(array $user, ResponseInterface $response) : bool
    {
        return true;
    }

}