<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureTrait;

class AuthCredentialsProcedure implements AuthProcedureInterface
{
    public const NAME = 'Credentials';

    public const DESCRIPTION = 'Provides an authentication based on credentials';

    /**
     * @var AuthCredentialsOptionsInterface
     */
    private $options;

    use AuthProcedureTrait;

    public function __construct(
        CredentialsProviderInterface $provider,
        AuthVerifierInterface $verifier,
        AuthCredentialsOptionsInterface $options
    )
    {
        $this->provider = $provider;
        $this->verifier = $verifier;
        $this->options = $options;

        $this->namespace = AuthProcedureInterface::NAMESPACE;
        $this->name = self::NAME;
        $this->description = self::DESCRIPTION;
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->options->getKey());
    }

    public function getSecretFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->options->getSecret());
    }

    public function validate(RequestInterface $request, ResponseInterface $response) : void
    {
        // TODO: Implement validateCredentials() method.
    }
}
