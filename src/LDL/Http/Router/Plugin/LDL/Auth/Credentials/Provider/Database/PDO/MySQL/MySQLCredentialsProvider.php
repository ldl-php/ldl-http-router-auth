<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\MySQL\AbstractCredentialsPDOProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\DuplicateUsernameException;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\UsernameAlreadyExistsException;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Exception\UsernameNotFoundException;

class MySQLCredentialsProvider extends AbstractCredentialsPDOProvider
{
    private const DEFAULT_TABLE = 'ldl_auth_credentials';
    private const DEFAULT_ENCRYPTION = \PASSWORD_DEFAULT;
    private const DEFAULT_OPTIONS = [];

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
     * @var int
     */
    private $encryption;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        \PDO $pdo,
        string $userRowName,
        string $passwordRowName,
        string $table = null,
        int $encryption = null,
        array $options = null
    )
    {
        parent::__construct($pdo);

        $this->userRowName = $userRowName;
        $this->passwordRowName = $passwordRowName;
        $this->table = $table ?? self::DEFAULT_TABLE;
        $this->encryption = $encryption ?? self::DEFAULT_ENCRYPTION;
        $this->options = $options ?? self::DEFAULT_OPTIONS;

    }

    public function fetch(...$args): ?array
    {
        [$username, $password] = $args;

        $user = $this->getUser($username);

        if($user && password_verify($password, $user[$this->passwordRowName])){
            unset($user[$this->passwordRowName]);
            return ['user' => $user];
        }

        return null;
    }

    public function generate(...$args)
    {
        [$username, $password] = $args;

        if(null !== $this->getUser($username)){
            $msg = "Username {$username} already exists! In database table {$this->table}";
            throw new UsernameAlreadyExistsException($msg);
        }

        $password = password_hash($password, $this->encryption, $this->options);

        $pdo = $this->getConnection();

        $sql = 'INSERT INTO :tableName SET :userRowName=:username, :passwordRowName=:password, createdAt=:createdAt';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':tableName' => $this->table,
            ':userRowName' => $this->userRowName,
            ':username' => $username,
            ':passwordRowName' => $this->passwordRowName,
            ':password' => $password,
            ':createdAt' => new \DateTime('now', new \DateTimeZone('UTC'))
        ]);
    }

    public function update(...$args)
    {
        [$username, $password] = $args;

        $user = $this->getUser($username);

        if(!$user){
            $msg = "Username {$username} was not found";
            throw new UsernameNotFoundException($msg);
        }

        $newUsername = $username ?? $user[$this->userRowName];
        $newPassword = $password ?? $user[$this->passwordRowName];

        $updatedAt = new \DateTime('now', new \DateTimeZone("UTC"));

        $pdo = $this->getConnection();

        $sql = 'UPDATE :tableName SET :userRowName=:username, :passwordRowName=:password, updatedAt=:updatedAt WHERE id=:id';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $user['id'],
            ':tableName' => $this->table,
            ':userRowName' => $this->userRowName,
            ':username' => $newUsername,
            ':passwordRowName' => $this->passwordRowName,
            ':password' => $newPassword,
            ':updatedAt' => $updatedAt
        ]);
    }

    private function getUser(string $username): ?array
    {
        $pdo = $this->getConnection();

        $stmt = $pdo->prepare('SELECT * FROM :tableName WHERE :userRowName = :username');
        $stmt->execute([
            ':tableName' => $this->table,
            ':userRowName' => $this->userRowName,
            ':username' => $username
        ]);

        if($stmt->rowCount() > 1){
            $msg = "Duplicate username found, usernames must be unique! In database table {$this->table}";
            throw new DuplicateUsernameException($msg);
        }

        return $stmt->rowCount() > 0 ? $stmt->fetch(\PDO::FETCH_ASSOC) : null;
    }
}