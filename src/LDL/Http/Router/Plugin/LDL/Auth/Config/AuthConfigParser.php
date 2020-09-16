<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Config;

use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\PreDispatch;
use LDL\Http\Router\Plugin\LDL\Auth\Provider\ProviderRepository;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserInterface;
use LDL\Http\Router\Route\Route;
use Psr\Container\ContainerInterface;

class AuthConfigParser implements RouteConfigParserInterface
{
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_PRIORITY = 1;

    /**
     * @var ProviderRepository
     */
    private $providers;

    public function __construct(
        ProviderRepository $providers
    )
    {
        $this->providers = $providers;
    }

    public function parse(
        array $data,
        Route $route,
        ContainerInterface $container = null,
        string $file = null
    ): void
    {
        if(!array_key_exists('auth', $data)){
            return;
        }

        $auth = $data['auth'];

        $isActive = self::DEFAULT_IS_ACTIVE;

        if(array_key_exists('active', $auth)){
            $isActive = (bool) $auth['active'];
        }

        $priority = self::DEFAULT_PRIORITY;

        if(array_key_exists('priority', $auth)){
            $priority = (int) $auth['priority'];
        }

        if(!array_key_exists('namespace', $auth)) {
            $msg = 'On auth section, missing namespace';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        if(!array_key_exists('name', $auth)) {
            $msg = 'On auth section, missing name';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        $provider = $this->providers->getProvider($auth['namespace'], $auth['name']);

        $preDispatch = new PreDispatch($provider, $isActive, $priority);

        $route->getConfig()->getPreDispatchMiddleware()->append($preDispatch);
    }
}