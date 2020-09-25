<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Auth;

interface AuthInterface
{
    /**
     * @param AuthenticationInterface $authentication
     * @return AuthInterface
     */
    public function setAuthentication(AuthenticationInterface $authentication) : AuthInterface;
}