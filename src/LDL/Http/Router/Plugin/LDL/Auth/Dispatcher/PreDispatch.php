<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Dispatcher;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Dispatcher\FinalDispatcher;
use LDL\Http\Router\Middleware\PreDispatchMiddlewareInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Provider\AuthenticationProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Provider\AuthCredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Provider\AuthTokenProviderInterface;
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
     * @var AuthenticationProviderInterface
     */
    private $authProvider;

    public function __construct(
        AuthenticationProviderInterface $authProvider,
        bool $isActive = null,
        int $priority = null
    )
    {
        $this->isActive = $isActive ?? self::DEFAULT_IS_ACTIVE;
        $this->priority = $priority ?? self::DEFAULT_PRIORITY;
        $this->authProvider = $authProvider;
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
        if($this->authProvider instanceof AuthCredentialsProviderInterface){
            $this->authProvider->validateCredentials(
                $response,
                $this->authProvider->extractUserFromRequest($request),
                $this->authProvider->extractPasswordFromRequest($request)
            );
        }

        if($this->authProvider instanceof AuthTokenProviderInterface){
            $this->authProvider->validateToken(
                $response,
                $this->authProvider->extractTokenFromRequest($request)
            );
        }

        return null;
    }
}