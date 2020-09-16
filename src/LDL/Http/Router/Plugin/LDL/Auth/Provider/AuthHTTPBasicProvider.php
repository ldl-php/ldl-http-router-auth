<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Provider;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;

class AuthHTTPBasicProvider implements AuthenticationProviderInterface, AuthCredentialsProviderInterface
{
    public const NAMESPACE = 'LDLAuthPlugin';

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

    use AuthenticationProviderTrait;

    public function __construct(
        CredentialsProviderInterface $consumer,
        string $realm='Authentication required',
        string $type = 'Basic'
    )
    {
        $this->namespace = self::NAMESPACE;
        $this->name = self::NAME;
        $this->description = self::DESCRIPTION;

        $this->type = $type;
        $this->realm = $realm;

        $this->consumer = $consumer;
    }

    public function extractPasswordFromRequest(RequestInterface $request): ?string
    {
        return $request->getPassword();
    }

    public function extractUserFromRequest(RequestInterface $request): ?string
    {
        return $request->getUser();
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