<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;

interface AuthProcedureInterface
{
    public const NAMESPACE = 'LDLAuthPlugin';

    /**
     * @return string
     */
    public function getNamespace() : string;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return mixed
     */
    public function getDescription() : string;

    /**
     * @return AuthVerifierInterface
     */
    public function getAuthVerifier() : AuthVerifierInterface;

    /**
     * @return CredentialsProviderInterface
     */
    public function getCredentialsProvider() : CredentialsProviderInterface;

    /**
     * @param RequestInterface $request
     * @return string|null
     */
    public function getKeyFromRequest(RequestInterface $request) : ?string;

    /**
     * @param RequestInterface $request
     * @return string|null
     */
    public function getSecretFromRequest(RequestInterface $request) : ?string;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function validate(
        RequestInterface $request,
        ResponseInterface $response
    ) : void;

}