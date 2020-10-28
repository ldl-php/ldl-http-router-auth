<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;

interface AuthProcedureInterface
{
    public function isDefault() : bool;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return mixed
     */
    public function getDescription() : string;

    /**
     * @return CredentialsProviderInterface
     */
    public function getCredentialsProvider() : CredentialsProviderInterface;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void;

}