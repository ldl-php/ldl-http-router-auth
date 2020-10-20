<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Handler\Exception\Collection\ExceptionHandlerCollection;
use LDL\Http\Router\Route\Dispatcher\RouteDispatcherInterface;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Plugin\LDL\Template\Config\TemplateConfigParser;
use LDL\Http\Router\Plugin\LDL\Template\Repository\TemplateFileRepository;
use LDL\Http\Router\Plugin\LDL\Template\Engine\Repository\TemplateEngineRepository;
use LDL\Http\Router\Plugin\LDL\Template\Engine\PhpTemplateEngine;
use LDL\Http\Router\Response\Parser\Repository\ResponseParserRepository;
use LDL\Http\Router\Plugin\LDL\Template\Response\TemplateResponseParser;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedureOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL\MySQLCredentialsProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Config\AuthConfigParser;
use LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception\AuthenticationExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\NeedsProcedureRepositoryInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\FalseVerifier;
use Symfony\Component\HttpFoundation\ParameterBag;

define('APP_ID', '326263348655004');
define('APP_SECRET', 'a596b7a8368dbfc71c36d0113305cdfc');

$dsn = 'mysql:host=localhost;dbname=ldl_auth';

$pdo = new \PDO($dsn,'root', 'viceroynextbic',[
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
]);

class Dispatcher implements RouteDispatcherInterface, NeedsProcedureRepositoryInterface
{
    /**
     * @var ProcedureRepository
     */
    private $procedures;

    public function setProcedureRepository(ProcedureRepository $repository)
    {
        $this->procedures = $repository;
    }

    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response,
        ParameterBag $urlParameters = null
    ): ?array
    {
        /**
         * @var FacebookProcedure $facebook
         */
        $facebook = $this->procedures
            ->filterByNamespaceAndName(FacebookProcedure::NAMESPACE, FacebookProcedure::NAME);

        return [
            'fb_login' => $facebook->getAuthorizationEndpoint()
        ];
    }

}

class LoginSuccess implements RouteDispatcherInterface, NeedsProcedureRepositoryInterface
{
    /**
     * @var ProcedureRepository
     */
    private $procedures;

    public function setProcedureRepository(ProcedureRepository $repository)
    {
        $this->procedures = $repository;
    }

    public function dispatch(
        RequestInterface $request,
        ResponseInterface $response,
        ParameterBag $urlParameters = null
    ): ?array
    {
        /**
         * @var FacebookProcedure $facebook
         */
        $facebook = $this->procedures
            ->filterByNamespaceAndName(FacebookProcedure::NAMESPACE, FacebookProcedure::NAME);

        return $facebook->getUserData()['response'];
    }

}

$templateFileRepository = new TemplateFileRepository(__DIR__.'/template');
$templateEngineRepository = new TemplateEngineRepository();

$templateEngineRepository->append(new PhpTemplateEngine(),'template.engine.php');

$responseParserRepository = new ResponseParserRepository();

$responseParserRepository->append(
        new TemplateResponseParser(
                $templateFileRepository,
                $templateEngineRepository
        ),
        'ldl.response.parser.template'
);

/**
 * Create a provider repository which holds different authentication methods
 */
$providers = new ProcedureRepository();

$providers->append(
    new FacebookProcedure(
        new MySQLCredentialsProvider(
            $pdo,
            'user',
            'password'
        ),
        new FacebookProcedureOptions(
            APP_ID,
            APP_SECRET,
            'http://localhost:8080/login/v1.0/verify'
        ),
        null
    ),
    'facebook.oauth2'
);

/**
 * Create the auth verifier repository, this repository holds distinct authentication verifiers
 * to validate authentication in subsequent requests.
 */
$verifiers = new AuthVerifierRepository();

$verifiers->append(
        new FalseVerifier(),
        'verifier.false'
);

/**
 * Add auth configuration parsing capabilities to route factory
 *
 * This basically adds the capability to the route factory to be able to parse specific authentication configuration
 * directives in the routes.json file.
 *
 * For example, which authentication verifier should be used to validate if a request is already authenticated or not.
 *
 */
$parserCollection = new RouteConfigParserCollection();

$parserCollection->append(
        new AuthConfigParser(
            $providers,
            $verifiers
        )
    )
    ->append(
        new TemplateConfigParser(
            $templateEngineRepository,
            $responseParserRepository
        )
    );

/**
 * Add global exception handler which handles AuthenticationRequired exceptions
 * This handler is in charge of responding 401 or 403, depending on which exception is thrown.
 */
$exceptionHandlers = new ExceptionHandlerCollection();
$exceptionHandlers->append(new AuthenticationExceptionHandler());


    $response = new Response();

    $router = new Router(
        Request::createFromGlobals(),
        $response,
        $exceptionHandlers,
        $responseParserRepository
    );

    $routes = RouteFactory::fromJsonFile(
        __DIR__ . '/routes.json',
        $router,
        null,
        $parserCollection
    );

    $group = new RouteGroup('test', 'login', $routes);

    $router->addGroup($group);
    $router->dispatch()->send();
