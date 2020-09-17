<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\CredentialsAdapterInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthCredentialsProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthenticationProcedureInterface;

class AuthCredentialsProcedure implements AuthenticationProcedureInterface, AuthCredentialsProcedureInterface
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

    /**
     * @var CredentialsProviderInterface
     */
    private $provider;

    /**
     * @var CredentialsAdapterInterface
     */
    private $adapter;

    public function __construct(
        CredentialsProviderInterface $provider,
        CredentialsAdapterInterface $adapter,
        string $usernameVariable,
        string $passwordVariable
    )
    {
        $this->usernameVariable = $usernameVariable;
        $this->passwordVariable = $passwordVariable;
        $this->provider = $provider;
        $this->adapter = $adapter;
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

    public function isAuthenticated(...$args): bool
    {
        return (bool) $this->adapter->isAuthenticated($args);
    }

    public function extractUserFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->usernameVariable);
    }

    public function extractPasswordFromRequest(RequestInterface $request): ?string
    {
        return $request->get($this->passwordVariable);
    }

    public function validateCredentials(ResponseInterface $response, string $username = null, string $password = null, ...$args)
    {
        if(null === $username || null === $password){
            $msg = "Missing value for {$this->usernameVariable} or {$this->passwordVariable}";
            throw new AuthenticationRequiredException($msg);
        }

        $user = null;

        try{
            $user = $this->provider->fetch($username, $password, $args);
        }catch(\Exception $e){
            $response->setStatusCode(ResponseInterface::HTTP_CODE_FORBIDDEN);
        }

        if(null === $user){
            throw new AuthenticationFailureException('Authentication failed');
        }

        return $user;
    }
}
