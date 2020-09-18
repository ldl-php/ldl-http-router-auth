<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

class AuthCredentialsOptions implements AuthCredentialsOptionsInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    public function __construct(
        string $key,
        string $secret
    )
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getSecret() : string
    {
        return $this->secret;
    }
}