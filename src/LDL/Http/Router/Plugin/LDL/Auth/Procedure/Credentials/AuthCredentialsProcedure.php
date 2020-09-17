<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthCredentialsProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthenticationProcedureInterface;

class AuthCredentialsMechanism implements AuthenticationProcedureInterface, AuthCredentialsProcedureInterface
{

    public const NAME = 'Credentials';

    /**
     * @var string
     */
    private $usernameVariable;

    /**
     * @var string
     */
    private $passwordVariable;

    public function __construct(
        CredentialsProviderInterface $provider,
        string $usernameVariable,
        string $passwordVariable
    )
    {
        $this->usernameVariable = $usernameVariable;
        $this->passwordVariable = $passwordVariable;
    }


    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return AuthenticationProcedureInterface::NAMESPACE;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return mixed
     */
    public function getDescription(): string
    {
        // TODO: Implement getDescription() method.
    }

    public function extractUserFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->usernameVariable);
    }

    public function extractPasswordFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->passwordVariable);
    }

    public function validateCredentials(ResponseInterface $response, string $username = null, string $password = null, array ...$args)
    {
        // TODO: Implement validateCredentials() method.
    }
}
