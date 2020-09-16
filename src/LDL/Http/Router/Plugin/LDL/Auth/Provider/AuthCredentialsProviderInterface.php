<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Provider;

use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;

interface AuthCredentialsProviderInterface
{
    public function extractUserFromRequest(RequestInterface $request) : ?string;

    public function extractPasswordFromRequest(RequestInterface $request) : ?string;

    public function validateCredentials(
        ResponseInterface $response,
        string $username=null,
        string $password=null,
        ...$args
    );
}

