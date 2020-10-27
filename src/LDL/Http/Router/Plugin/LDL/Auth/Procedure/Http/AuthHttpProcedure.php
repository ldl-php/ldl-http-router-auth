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
    public const NAME = 'ldl.auth.http.basic.auth';
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
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);

        $this->options = $options ?? new AuthHttpProcedureOptions();
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->getHeaderBag()->get('php-auth-user');
    }

    public function getSecretFromRequest(RequestInterface $request): ?string
    {
        return $request->getHeaderBag()->get('php-auth-pw');
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
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