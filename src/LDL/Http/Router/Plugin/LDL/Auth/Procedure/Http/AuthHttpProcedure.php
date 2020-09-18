<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureTrait;

class AuthHttpProcedure implements AuthProcedureInterface
{
    public const NAME = 'HTTP Basic Auth';

    public const DESCRIPTION = 'Provides HTTP basic authentication';

    /**
     * @var AuthHttpProcedureOptionsInterface
     */
    private $options;

    use AuthProcedureTrait;

    public function __construct(
        CredentialsProviderInterface $provider,
        AuthVerifierInterface $verifier,
        AuthHttpProcedureOptionsInterface $options=null
    )
    {
        $this->verifier = $verifier;
        $this->provider = $provider;
        $this->options = $options ?? new AuthHttpProcedureOptions();

        $this->namespace = AuthProcedureInterface::NAMESPACE;
        $this->name = self::NAME;
        $this->description = self::DESCRIPTION;
    }

    public function getSecretFromRequest(RequestInterface $request): ?string
    {
        return $request->getPassword();
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->getUser();
    }

    public function validate(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if(null === $username || null === $password){
            $response->getHeaderBag()
                ->set(
                    'WWW-Authenticate',
                    sprintf(
                        '%s realm="%s"',
                        $this->options->getType(),
                        $this->options->getRealm()
                    )
                );

            throw new AuthenticationRequiredException($this->options->getRealm());
        }

        if(null === $this->provider->fetch($username, $password)){
            throw new AuthenticationFailureException('Authentication failed');
        }
    }

}