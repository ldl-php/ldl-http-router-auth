<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\PDO;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken\LDLTokenGeneratorOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator\RandomHashGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator\RandomHashGenerator;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\UserDataInterface;

class LDLTokenPDOGenerator implements TokenGeneratorInterface
{
    private const NAME = 'ldl.auth.token.pdo.generator';

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
        ResponseInterface $response,
        AuthProcedureInterface $authProcedure
    ) : string
    {
        $token = $this->hashGenerator->generate();
        $headers = $this->options->getHeaders();

        $sql = sprintf(
            'INSERT INTO `%s` SET `user`=:user, `token`=:token, `createdAt`=:createdAt, `expiresAt`=:expiresAt, `provider_name`=:providerName, `provider_data`=:providerData',
            $this->tableName
        );

        $stmt = $this->connection->prepare($sql);

        $utcTZ = new \DateTimeZone('UTC');
        $now = new \DateTime('now', $utcTZ);
        $expiresAt = $now->add($this->options->getExpiresAt());

        $extraData = null;

        if($authProcedure instanceof UserDataInterface){
            $extraData = json_encode($authProcedure->getUserData(), JSON_THROW_ON_ERROR);
        }

        $stmt->execute([
            ':token' => $token,
            ':user' => $user['user'],
            ':createdAt' => $now->format('Y-m-d H:i:s'),
            ':expiresAt' => $expiresAt->format('Y-m-d H:i:s'),
            ':providerName' => $authProcedure->getName(),
            ':providerData' => $extraData
        ]);

        $response->getHeaderBag()->set($headers['token'], $token);
        $response->getHeaderBag()->set($headers['refresh'], $this->options->getRefreshEndpoint());
        $response->getHeaderBag()->set($headers['expiresAt'], $expiresAt->format(\DateTimeInterface::RFC3339_EXTENDED));

        return $token;
    }

    public function destroy(array $user, ResponseInterface $response, AuthProcedureInterface $authProcedure) : bool
    {
        return true;
    }

}