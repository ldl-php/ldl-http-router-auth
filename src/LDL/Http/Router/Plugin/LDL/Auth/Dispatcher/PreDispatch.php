<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Dispatcher;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Dispatcher\FinalDispatcher;
use LDL\Http\Router\Middleware\PreDispatchMiddlewareInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Route\Route;

class PreDispatch implements PreDispatchMiddlewareInterface, FinalDispatcher
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

    public function __construct(
        AuthProcedureInterface $authProcedure,
        AuthVerifierInterface $authVerifier,
        TokenGeneratorInterface $tokenGenerator=null,
        bool $isActive = null,
        int $priority = null
    )
    {
        $this->authProcedure = $authProcedure;
        $this->authVerifier = $authVerifier;
        $this->tokenGenerator = $tokenGenerator;
        $this->isActive = $isActive ?? self::DEFAULT_IS_ACTIVE;
        $this->priority = $priority ?? self::DEFAULT_PRIORITY;
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
    ) :?string
    {

        $userIdentifier = $this->authProcedure->getKeyFromRequest($request);

        /*
        yo me logueo con usuario y contraseña en el endpoint /login
        aca cuando el login es exitoso, *se crea un token* <-
        este token: *se manda en el response como X-LDL-Auth-Token: ajksdlkjaksldjklajsdklkjasd*

        Para otras rutas especifico que el verifier es el token verifier (OK)
        Es el token otro autentication procedure? Si, es un procedure diferente

        Cuando el login es exitoso, como se crea el token ? Utilizando un token generator
        */

        /**
         * User is authenticated
         */
        if(null !== $userIdentifier && $this->authVerifier->isAuthenticated($userIdentifier)){
            return null;
        }

        /**
         * Handle authentication
         */
        $this->authProcedure->handle($request, $response);

        /**
         * Successful login, if there's a token generator, generate it
         */
        if(null !== $this->tokenGenerator){
            $this->tokenGenerator->create($response);
        }

        return null;
    }
}