<?php declare(strict_types=1);

namespace LDL\Router\Plugin\LDL\Auth\Credentials\Verifier;

class MySQLVerifier
{
    public const TOKEN_TABLE_NAME = 'ldl_auth_token';

    public const TOKEN_FIELD_NAME = 'token';

    public const TOKEN_EXPIRY_FIELD = 'expiresAt';

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $tokenField;

    /**
     * @var string
     */
    private $dateTimeField;

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(
        \PDO $pdo,
        string $table = null,
        string $tokenField = null,
        string $dateTimeField = null
    )
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->tokenField = $tokenField ?? self::TOKEN_FIELD_NAME;
        $this->dateTimeField = $dateTimeField ?? self::TOKEN_EXPIRY_FIELD;
    }

    public function isAuthenticated(string $username) : bool
    {

    }
}