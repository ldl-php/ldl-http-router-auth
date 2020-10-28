<?php declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

use LDL\Http\Core\Request\Request;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\Response;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Route\Factory\RouteFactory;
use LDL\Http\Router\Route\Group\RouteGroup;
use LDL\Http\Router\Route\RouteInterface;
use LDL\Http\Router\Router;
use LDL\Http\Router\Route\Config\Parser\RouteConfigParserCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Config\AuthConfigParser;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\ProcedureRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http\AuthHttpProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain\PlainFileCredentialsProvider;
use LDL\Http\Router\Handler\Exception\Collection\ExceptionHandlerCollection;
use LDL\Http\Router\Plugin\LDL\Auth\Handler\Exception\AuthenticationExceptionHandler;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierRepository;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\FalseVerifier;
use LDL\Http\Router\Middleware\AbstractMiddleware;
use Symfony\Component\HttpFoundation\ParameterBag;

class Dispatcher extends AbstractMiddleware
{
    public function isActive(): bool
    {
        return true;
    }

    public function getPriority(): int
    {
        return 1;
    }

    public function dispatch(
        RouteInterface $route,
        RequestInterface $request,
        ResponseInterface $response,
        ParameterBag $urlParameters = null
    ): ?array
    {
        return [
            'age' => (int) $request->get('age'),
            'name' => $request->get('name')
        ];
    }
}

/**
 * Create a provider repository which holds different authentication methods
 */
$providers = new ProcedureRepository();

/**
 * In this case we append a PlainFileCredentialsProvider with no ciphering options
 * You can check the users.txt in this very same folder to get valid login credentials.
 */

$providers->append(
    new AuthHttpProcedure(
        new PlainFileCredentialsProvider(
            'users.txt'
        ),
        null,
        true
    ),
    'ldl.auth.http.basic.auth'
);

/**
 * Create the auth verifier repository, this repository holds distinct authentication verifiers
 * to validate authentication in subsequent requests.
 */
$verifiers = new AuthVerifierRepository();

/**
 * In this case we only add the false verifier, since by default HTTP Basic authentication requires
 * username and password for every request.
 *
 * It's important to note that this statement here doesn't selects the authentication verifier, it just appends
 * a verifier to the collection.
 *
 * The task of selecting the authentication verifier depends on the route configuration.
 *
 * See file ./routes.json
 */
$verifiers->append(new FalseVerifier(), 'ldl.auth.false.verifier');

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
$parserCollection->append(new AuthConfigParser($providers, $verifiers));

/**
 * Add global exception handler which handles AuthenticationRequired exceptions
 * This handler is in charge of responding 401 or 403, depending on which exception is thrown.
 */
$exceptionHandlers = new ExceptionHandlerCollection();
$exceptionHandlers->append(new AuthenticationExceptionHandler());

try {
    $response = new Response();

    $router = new Router(
        Request::createFromGlobals(),
        $response,
        $exceptionHandlers
    );

    $router->getDispatcherChain()
        ->append(new Dispatcher('http.dispatcher'));

    $routes = RouteFactory::fromJsonFile(
        __DIR__ . '/routes.json',
        $router,
        null,
        $parserCollection
    );

    $group = new RouteGroup('test', 'student', $routes);

    $router->addGroup($group);
    $router->dispatch()->send();

}catch(\Exception $e){
    echo $e->getMessage();
}

