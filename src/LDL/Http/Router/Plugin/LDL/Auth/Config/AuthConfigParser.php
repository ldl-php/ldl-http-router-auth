<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Config;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\PreDispatch;
use LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception\AuthenticationExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\NeedsProcedureRepositoryInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserInterface;
use LDL\Http\Router\Route\Route;
use LDL\Http\Router\Route\RouteInterface;
use Psr\Container\ContainerInterface;

class AuthConfigParser implements RouteConfigParserInterface
{
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_PRIORITY = 1;

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
        ProcedureRepository $procedures,
        AuthVerifierRepository $verifiers,
        TokenGeneratorRepository $generators = null
    )
    {
        $this->verifiers = $verifiers;
        $this->procedures = $procedures;
        $this->generators = $generators;
    }

    public function parse(
        array $data,
        RouteInterface $route,
        ContainerInterface $container = null,
        string $file = null
    ) : void
    {
        /**
         * If auth key does not exists in the route configuration
         * assume no authentication is required.
         */
        if(!array_key_exists('auth', $data)){
            return;
        }

        /**
         * Add exception handler for authentication
         * Arguments such as exception handler priority could come from the config array
         */
        $route->getRouter()
            ->getExceptionHandlerCollection()
            ->append(new AuthenticationExceptionHandler(
                'LDLAuth',
                'AuthExceptionHandler',
                1,
                true
            ));

        $dispatcher = $route->getConfig()->getDispatcher();

        if($dispatcher instanceof NeedsProcedureRepositoryInterface) {
            $dispatcher->setProcedureRepository($this->procedures);
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
            $msg = 'Missing authentication procedure';
            throw new Exception\MissingProcedureException($msg);
        }

        $tokenGenerator = null !== $this->generators ? $this->getTokenGenerator($auth) : null;

        $preDispatch = new PreDispatch(
            $procedure,
            $verifier,
            $tokenGenerator,
            $isActive,
            $priority,
            true
        );

        $route->getConfig()->getPreDispatchMiddleware()->append($preDispatch);
    }

    //<editor-fold desc="Private methods">

    private function getProcedure(array $auth) : ?AuthProcedureInterface
    {
        if(!array_key_exists('procedure', $auth)) {
            return null;
        }

        $procedure = $auth['procedure'];

        if(!is_string($procedure)) {
            $msg = sprintf(
                'Auth procedure must be a string, "%s" was given',
                gettype($procedure)
            );

            throw new Exception\AuthConfigParserSectionException($msg);
        }

        /**
         * @var AuthProcedureInterface|null $provider
         */
        $this->procedures->select($procedure);

        return $this->procedures->getSelectedItem();
    }

    private function getVerifier(array $auth) : ?AuthVerifierInterface
    {
        if(!array_key_exists('verifier', $auth)) {
            return null;
        }

        $verifier = $auth['verifier'];

        if(!is_string($verifier)) {
            $msg = sprintf(
                'Auth verifier must be a string, "%s" was given',
                gettype($verifier)
            );

            throw new Exception\AuthConfigParserSectionException($msg);
        }

        $this->verifiers->select($verifier);

        /**
         * @var AuthVerifierInterface $return
         */
        $return = $this->verifiers->getSelectedItem();

        return $return;
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

        /**
         * @var TokenGeneratorInterface $generator
         */
        $generator = $this->generators
            ->filterByNamespaceAndName(
                $token['generator']['namespace'],
                $token['generator']['name']
            );

        return $generator;
    }
    //</editor-fold>
}