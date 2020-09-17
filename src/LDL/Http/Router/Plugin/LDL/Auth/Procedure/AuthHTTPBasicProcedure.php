<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter\CredentialsAdapterInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;

class AuthHTTPBasicProcedure implements AuthenticationProcedureInterface, AuthCredentialsProcedureInterface
{
    public const NAME = 'HTTP Basic Auth';

    public const DESCRIPTION = 'Provides HTTP basic authentication';

    /**
     * @var CredentialsProviderInterface
     */
    private $consumer;

    /**
     * @var string
     */
    private $realm;

    /**
     * @var string
     */
    private $type;

    /**
     * @var CredentialsAdapterInterface
     */
    private $adapter;

    use AuthenticationProcedureTrait;

    public function __construct(
        CredentialsProviderInterface $consumer,
        CredentialsAdapterInterface $adapter,
        string $realm='Authentication required',
        string $type = 'Basic'
    )
    {
        $this->namespace = AuthenticationProcedureInterface::NAMESPACE;
        $this->name = self::NAME;
        $this->description = self::DESCRIPTION;

        $this->type = $type;
        $this->realm = $realm;

        $this->consumer = $consumer;
        $this->adapter = $adapter;
    }

    public function extractPasswordFromRequest(RequestInterface $request): ?string
    {
        return $request->getPassword();
    }

    public function extractUserFromRequest(RequestInterface $request): ?string
    {
        return $request->getUser();
    }

    public function isAuthenticated(...$args): bool
    {
        return (bool) $this->adapter->isAuthenticated($args);
    }

    public function validateCredentials(
        ResponseInterface $response,
        string $username=null,
        string $password=null,
        ...$args
    )
    {
        if(null === $username || null === $password){
            $value = sprintf('%s realm="%s"', $this->type, $this->realm);
            $response->getHeaderBag()->set('WWW-Authenticate',$value);
            throw new AuthenticationRequiredException($this->realm);
        }

        if(null === $this->consumer->fetch($username, $password)){
            throw new AuthenticationFailureException('Authentication failed');
        }
    }

}