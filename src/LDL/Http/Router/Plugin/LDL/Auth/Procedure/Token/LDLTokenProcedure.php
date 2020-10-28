<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials\Token;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AbstractAuthProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestKeyInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Token\LDLTokenOptionsInterface;

class LDLTokenProcedure extends AbstractAuthProcedure implements RequestKeyInterface
{
    public const NAME = 'ldl.token.credentials';
    public const DESCRIPTION = 'Provides an authentication based on a token set in the request headers';

    /**
     * @var LDLTokenOptionsInterface
     */
    private $options;

    public function __construct(
        CredentialsProviderInterface $provider,
        LDLTokenOptionsInterface $options=null,
        bool $isDefault = false
    )
    {
        $this->options = $options;

        $this->setCredentialsProvider($provider)
            ->setDefault($isDefault)
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        if($this->options->isFromHeaders()){
            return $request->getHeaderBag()->get($this->options->getKey());
        }

        return $request->get($this->options->getKey());
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
        // TODO: Implement validateCredentials() method.
    }
}
