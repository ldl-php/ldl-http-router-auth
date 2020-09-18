<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier;

interface AuthVerifierInterface
{
    public function isAuthenticated(string $username) : bool;
}