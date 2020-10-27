<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Client\FacebookClientInterface;

class FacebookAuthVerifier implements AuthVerifierInterface
{

    private $fbClient;

    public function __construct(FacebookClientInterface $client)
    {
        $this->fbClient = $client;
    }

    public function getNamespace() : string
    {
        return 'LDLAuthPlugin';
    }

    public function getName() : string
    {
        return 'Facebook Verifier';
    }

    public function isAuthenticated(string $code): bool
    {
        return null !== $this->fbClient->getUserData($code);
    }
}