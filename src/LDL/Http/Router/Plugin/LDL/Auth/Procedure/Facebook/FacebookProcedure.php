<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AbstractAuthProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestKeyInterface;

class FacebookProcedure extends AbstractAuthProcedure implements RequestKeyInterface
{
    public const NAMESPACE = 'FacebookPlugin';
    public const NAME = 'Credentials';
    public const DESCRIPTION = 'Provides an authentication based on Facebook oauth';

    public function __construct(CredentialsProviderInterface $provider, bool $isDefault = false)
    {
        $this->setCredentialsProvider($provider)
            ->setDefault($isDefault)
            ->setNamespace(self::NAMESPACE)
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        return $request->get('code', '');
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
        if(!$request->get('code')){
            throw new AuthenticationRequiredException('Authentication required');
        }
    }

}