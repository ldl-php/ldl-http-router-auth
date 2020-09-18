<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;

trait AuthProcedureTrait
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var CredentialsProviderInterface
     */
    private $provider;

    /**
     * @var AuthVerifierInterface
     */
    private $verifier;

    /**
     * @var string
     */
    private $description;

    public function getNamespace() : string
    {
        return $this->namespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAuthVerifier() : AuthVerifierInterface
    {
        return $this->verifier;
    }

    public function getCredentialsProvider() : CredentialsProviderInterface
    {
        return $this->provider;
    }

}