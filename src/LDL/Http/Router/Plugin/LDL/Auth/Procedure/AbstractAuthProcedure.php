<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;

abstract class AbstractAuthProcedure implements AuthProcedureInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CredentialsProviderInterface
     */
    private $provider;

    /**
     * @var string
     */
    private $description;

    /**
     * @var bool
     */
    private $isDefault;

    public function isDefault() : bool
    {
        return $this->isDefault;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCredentialsProvider() : CredentialsProviderInterface
    {
        return $this->provider;
    }

    protected function setDefault(bool $default) : self
    {
        $this->isDefault = $default;
        return $this;
    }

    protected function setCredentialsProvider(CredentialsProviderInterface $credentialsProvider) : self
    {
        $this->provider = $credentialsProvider;
        return $this;
    }

    protected function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    protected function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }
}