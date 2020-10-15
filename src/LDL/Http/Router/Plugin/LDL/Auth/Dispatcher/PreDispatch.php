<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Dispatcher;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Middleware\MiddlewareInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestKeyInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestSecretInterface;
use LDL\Http\Router\Route\Route;

class PreDispatch implements MiddlewareInterface
{
    private const NAMESPACE = 'LDLPlugin';
    private const NAME = 'Authentication';
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_PRIORITY = 1;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var AuthProcedureInterface
     */
    private $authProcedure;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;

    /**
     * @var AuthVerifierInterface
     */
    private $authVerifier;

    /**
     * @var bool
     */
    private $autoRegister;

    public function __construct(
        AuthProcedureInterface $authProcedure,
        AuthVerifierInterface $authVerifier,
        TokenGeneratorInterface $tokenGenerator=null,
        bool $isActive = null,
        int $priority = null,
        bool $autoRegister = false
    )
    {
        $this->authProcedure = $authProcedure;
        $this->authVerifier = $authVerifier;
        $this->tokenGenerator = $tokenGenerator;
        $this->isActive = $isActive ?? self::DEFAULT_IS_ACTIVE;
        $this->priority = $priority ?? self::DEFAULT_PRIORITY;
        $this->autoRegister = $autoRegister;
    }

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function dispatch(
        Route $route,
        RequestInterface $request,
        ResponseInterface $response,
        array $urlArguments = []
    ) :?array
    {
        $userIdentifier = null;
        $secret = null;
        $credentialsProvider = $this->authProcedure->getCredentialsProvider();

        if($this->authProcedure instanceof RequestKeyInterface) {
            $userIdentifier = $this->authProcedure->getKeyFromRequest($request);
        }

        if($this->authProcedure instanceof RequestSecretInterface){
            $secret = $this->authProcedure->getSecretFromRequest($request);
        }

        if(null !== $userIdentifier && $this->authVerifier->isAuthenticated($userIdentifier)){
            /**
             * User is authenticated
             */
            return null;
        }

        $user = $credentialsProvider->validate($userIdentifier, $secret);

        if(null === $user){
            /**
             * Handle authentication
             */
            $this->authProcedure->handle($request, $response);

            if($this->autoRegister && !$credentialsProvider->fetch($userIdentifier)){
                $credentialsProvider->create($userIdentifier, $secret);
            }

            $user = $credentialsProvider->fetch($userIdentifier);
        }

        /**
         * Successful login, if there's a token generator, generate it
         */
        if(null !== $this->tokenGenerator){
            $this->tokenGenerator->create($user, $response, $this->authProcedure);
        }

        return null;
    }
}