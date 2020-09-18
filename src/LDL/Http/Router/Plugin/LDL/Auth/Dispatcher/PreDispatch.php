<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Dispatcher;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Dispatcher\FinalDispatcher;
use LDL\Http\Router\Middleware\PreDispatchMiddlewareInterface;
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

    public function __construct(
        AuthProcedureInterface $authProcedure,
        bool $isActive = null,
        int $priority = null
    )
    {
        $this->isActive = $isActive ?? self::DEFAULT_IS_ACTIVE;
        $this->priority = $priority ?? self::DEFAULT_PRIORITY;
        $this->authProcedure = $authProcedure;
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

        if(null !== $userIdentifier && $this->authProcedure->getAuthVerifier()->isAuthenticated($userIdentifier)){
            return null;
        }

        $this->authProcedure->validate($request, $response);

        return null;
    }
}