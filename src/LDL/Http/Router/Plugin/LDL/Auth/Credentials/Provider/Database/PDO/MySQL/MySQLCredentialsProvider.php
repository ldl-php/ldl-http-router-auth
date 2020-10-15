<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL;

use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\AbstractCredentialsPDOProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\DuplicateUsernameException;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\UsernameAlreadyExistsException;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\UsernameNotFoundException;

class MySQLCredentialsProvider extends AbstractCredentialsPDOProvider
{
    private const DEFAULT_TABLE = 'ldl_auth_credentials';

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $userRowName;

    /**
     * @var string
     */
    private $passwordRowName;

    /**
     * @var CipherProviderInterface
     */
    private $cipherProvider;

    public function __construct(
        \PDO $pdo,
        string $userRowName,
        string $passwordRowName,
        string $table = null,
        CipherProviderInterface $cipherProvider = null
    )
    {
        parent::__construct($pdo);

        $this->userRowName = $userRowName;
        $this->passwordRowName = $passwordRowName;
        $this->table = $table ?? self::DEFAULT_TABLE;
        $this->cipherProvider = $cipherProvider ?? new CipherProvider(new CipherProviderOptions());
    }

    public function fetch(...$args): ?array
    {
        [$username] = $args;

        $pdo = $this->getConnection();

        $sql = sprintf(
            'SELECT * FROM `%s` WHERE `%s`=:username',
            $this->table,
            $this->userRowName
        );

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        if($stmt->rowCount() > 1){
            $msg = "Duplicate username found, user names must be unique! In database table {$this->table}";
            throw new DuplicateUsernameException($msg);
        }

        return $stmt->rowCount() > 0 ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
    }

    public function create(
        string $username,
        string $password=null,
        ...$args
    ) : bool
    {
        if(null !== $this->fetch($username)){
            $msg = "Username {$username} already exists! In database table {$this->table}";
            throw new UsernameAlreadyExistsException($msg);
        }

        $password = $this->cipherProvider->hash($password);

        $pdo = $this->getConnection();

        $sql = sprintf(
            'INSERT INTO `%s` SET `%s`=:username, `%s`=:password, `createdAt`=NOW()',
            $this->table,
            $this->userRowName,
            $this->passwordRowName
        );

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':username' => $username,
            ':password' => $password
        ]);

        return true;
    }

    public function update(...$args) : void
    {
        [$username, $password] = $args;

        $user = $this->fetch($username);

        if(!$user){
            $msg = "Username {$username} was not found";
            throw new UsernameNotFoundException($msg);
        }

        $newUsername = $username ?? $user[$this->userRowName];
        $newPassword = $password ?? $user[$this->passwordRowName];

        $pdo = $this->getConnection();

        $sql = sprintf(
            'UPDATE `%s` SET `%s`=:username, `%s`=:password, `updatedAt`=:updatedAt WHERE `id`=:id',
            $this->table,
            $this->userRowName,
            $this->passwordRowName
        );

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $user['id'],
            ':username' => $newUsername,
            ':password' => $newPassword,
            ':updatedAt' => 'NOW()'
        ]);
    }

    public function validate(...$args): ?array
    {
        [$username, $password] = $args;

        if(null === $password){
            return null;
        }

        $user = $this->fetch($username);

        if($user && $this->cipherProvider->compare($password, $user[$this->passwordRowName])){
            return $user;
        }

        return null;
    }

}