<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AbstractAuthProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestKeyInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestSecretInterface;

class AuthHttpProcedure extends AbstractAuthProcedure implements RequestKeyInterface, RequestSecretInterface
{
    public const NAME = 'HTTP Basic Auth';

    public const DESCRIPTION = 'Provides HTTP basic authentication';

    /**
     * @var AuthHttpProcedureOptionsInterface
     */
    private $options;

    public function __construct(
        CredentialsProviderInterface $provider,
        AuthHttpProcedureOptionsInterface $options=null,
        bool $isDefault = false
    )
    {
        $this->setCredentialsProvider($provider)
            ->setDefault($isDefault)
            ->setNamespace(AuthProcedureInterface::NAMESPACE)
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);

        $this->options = $options ?? new AuthHttpProcedureOptions();
    }

    public function getSecretFromRequest(RequestInterface $request): ?string
    {
        return $request->getPassword();
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->getUser();
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {

        $credentials = $this->getCredentialsProvider();

        if(null !== $credentials->validate($this->getKeyFromRequest($request), $this->getSecretFromRequest($request))){
            return;
        }

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

}