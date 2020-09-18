<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider;

class CipherProviderOptions implements CipherProviderOptionsInterface
{
    private $cipher;

    /**
     * @var array
     */
    private $parameters;

    public function __construct($cipher = null, array $parameters = null)
    {
        $this->cipher = $cipher ?? \PASSWORD_DEFAULT;
        $this->parameters = $parameters ?? [];
    }

    public function getCipher()
    {
        return $this->cipher;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}