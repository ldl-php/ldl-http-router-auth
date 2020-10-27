<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Middleware\AbstractMiddleware;
use LDL\Http\Router\Route\RouteInterface;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Plugin\LDL\Template\Config\TemplateConfigParser;
use LDL\Http\Router\Plugin\LDL\Template\Engine\Repository\TemplateEngineRepository;
use LDL\Http\Router\Plugin\LDL\Template\Engine\PhpTemplateEngine;
use LDL\Http\Router\Response\Parser\Repository\ResponseParserRepository;
use LDL\Http\Router\Plugin\LDL\Template\Response\TemplateResponseParser;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\Database\PDO\MySQL\MySQLCredentialsProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Config\AuthConfigParser;
use Symfony\Component\HttpFoundation\ParameterBag;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\FacebookAuthVerifier;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Client\FacebookClient;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookClientOptions;
use LDL\Http\Router\Plugin\LDL\Template\Finder\TemplateFileFinder;

define('APP_ID', '');
define('APP_SECRET', '');

$dsn = 'mysql:host=localhost;dbname=ldl_auth';

$pdo = new \PDO($dsn,'root', '',[
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
]);

class Dispatcher extends AbstractMiddleware
{
    /**
     * @var ProcedureRepository
     */
    private $repository;

    public function isActive(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function setProcedureRepository(ProcedureRepository $repository)
    {
        $this->repository = $repository;
    }

    public function dispatch(
        RouteInterface $route,
        RequestInterface $request,
        ResponseInterface $response,
        ParameterBag $urlParameters = null
    ): ?array
    {
        /**
         * @var FacebookProcedure $facebook
         */
        $facebook = $this->repository->offsetGet('facebook.oauth2');

        return [
            'fb_login' => 'dialog/oauth'
        ];
    }

}

class LoginSuccess extends AbstractMiddleware
{
    /**
     * @var ProcedureRepository
     */
    private $repository;

    /**
     * @var AuthVerifierRepository
     */
    private $verifier;

    public function isActive(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function setProcedureRepository(ProcedureRepository $repository)
    {
        $this->repository = $repository;
    }

    public function setVerifier(AuthVerifierRepository $verifier)
    {
        $this->verifier = $verifier;
    }

    public function dispatch(
        RouteInterface $route,
        RequestInterface $request,
        ResponseInterface $response,
        ParameterBag $urlParameters = null
    ): ?array
    {

        /**
         * @var FacebookAuthVerifier $verifier
         */
        $verifier = $this->verifier->offsetGet('facebook.verifier');

        /**
         * @var FacebookProcedure $facebook
         */
        $facebook = $this->repository->offsetGet('facebook.oauth2');

        return $verifier->getClient()->getUserData($facebook->getKeyFromRequest($request))['response'];
    }

}

$templateFileRepository = new TemplateFileFinder(__DIR__.'/template');
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
        )
    ),
    'facebook.oauth2'
);

/**
 * Create the auth verifier repository, this repository holds distinct authentication verifiers
 * to validate authentication in subsequent requests.
 */
$verifiers = new AuthVerifierRepository();

$verifiers->append(
    new FacebookAuthVerifier(
        new FacebookClient(
            new FacebookClientOptions(
                APP_ID,
                APP_SECRET,
                'http://localhost:8080/login/v1.0/verify'
            )
        )
    ),
    'facebook.verifier'
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

$dispatcher = new Dispatcher('dispatcher');
$dispatcher->setProcedureRepository($providers);

$loginDispatcher = new LoginSuccess('login.success.dispatcher');
$loginDispatcher->setProcedureRepository($providers);
$loginDispatcher->setVerifier($verifiers);

$response = new Response();

$router = new Router(
    Request::createFromGlobals(),
    $response,
    null,
    $responseParserRepository
);

$router->getDispatcherChain()
    ->append($dispatcher)
    ->append($loginDispatcher);

$routes = RouteFactory::fromJsonFile(
    __DIR__ . '/routes.json',
    $router,
    null,
    $parserCollection
);

$group = new RouteGroup('test', 'login', $routes);

$router->addGroup($group);
$router->dispatch()->send();
