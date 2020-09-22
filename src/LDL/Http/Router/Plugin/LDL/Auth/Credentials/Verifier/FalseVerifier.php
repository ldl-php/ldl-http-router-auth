<?php declare(strict_types=1);

/**
 * This verifier will always return false, it is useful for authentication methods
 * which require constant re validation such as HttpBasicAuth
 */

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier;

class FalseVerifier implements AuthVerifierInterface
{

    public function getNamespace() : string
    {
        return 'LDLAuthPlugin';
    }

    public function getName() : string
    {
        return 'False Verifier';
    }

    public function isAuthenticated(string $username): bool
    {
        return false;
    }
}