<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Config;

use LDL\Http\Router\Plugin\LDL\Auth\Auth\AuthenticationInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\PreDispatch;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserInterface;
use LDL\Http\Router\Route\Route;
use Psr\Container\ContainerInterface;

class AuthConfigParser implements RouteConfigParserInterface
{
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_PRIORITY = 1;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var ProcedureRepository
     */
    private $procedures;

    /**
     * @var AuthVerifierRepository
     */
    private $verifiers;

    /**
     * @var TokenGeneratorRepository
     */
    private $generators;

    public function __construct(
        AuthenticationInterface $authentication,
        ProcedureRepository $procedures,
        AuthVerifierRepository $verifiers,
        TokenGeneratorRepository $generators = null
    )
    {
        $this->authentication = $authentication;
        $this->verifiers = $verifiers;
        $this->procedures = $procedures;
        $this->generators = $generators;
    }

    public function parse(
        array $data,
        Route $route,
        ContainerInterface $container = null,
        string $file = null
    ): void
    {
        /**
         * If auth key does not exists in the route configuration
         * assume no authentication is required.
         */
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

        $verifier = $this->getVerifier($auth);

        if(null === $verifier){
            $msg = 'Missing verifier, in "auth" section';
            throw new Exception\MissingVerifierException($msg);
        }

        $procedure = $this->getProcedure($auth);

        if(null === $procedure){
            $msg = 'Missing authentication procedure, and no default authentication procedure was found';
            throw new Exception\MissingProcedureException($msg);
        }

        $this->authentication->setProcedure($procedure)
            ->setVerifier($verifier);

        $tokenGenerator = null !== $this->generators ? $this->getTokenGenerator($auth) : null;

        $preDispatch = new PreDispatch(
            $this->authentication,
            $procedure,
            $verifier,
            $tokenGenerator,
            $isActive,
            $priority,
            true
        );

        $route->getConfig()->getPreDispatchMiddleware()->append($preDispatch);
    }

    private function getProcedure(array $auth) : ?AuthProcedureInterface
    {
        if(!array_key_exists('procedure', $auth)) {
            return null;
        }

        $procedure = $auth['procedure'];

        if(!array_key_exists('namespace', $procedure)) {
            $msg = 'On auth section, missing namespace';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        if(!array_key_exists('name', $procedure)) {
            $msg = 'On auth section, missing name';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        $provider = $this->procedures->getProcedure($procedure['namespace'], $procedure['name']);

        return $provider ?? $this->procedures->getDefault();
    }

    private function getVerifier(array $auth) : ?AuthVerifierInterface
    {
        if(!array_key_exists('verifier', $auth)) {
            return null;
        }

        $verifier = $auth['verifier'];

        if(!array_key_exists('namespace', $verifier)) {
            $msg = 'On auth section verifier, missing namespace';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        if(!array_key_exists('name', $verifier)) {
            $msg = 'On auth section verifier, missing name';
            throw new Exception\AuthConfigParserSectionException($msg);
        }

        return $this->verifiers->getVerifier($verifier['namespace'], $verifier['name']);
    }

    private function getTokenGenerator(array $auth) : ?TokenGeneratorInterface
    {
        if(!array_key_exists('token', $auth)){
            return null;
        }

        $token = $auth['token'];

        if(!array_key_exists('generator', $token)){
            return null;
        }

        return $this->generators->getGenerator($token['generator']['namespace'], $token['generator']['name']);
    }
}