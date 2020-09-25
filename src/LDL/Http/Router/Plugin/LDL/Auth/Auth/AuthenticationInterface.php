<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Auth;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;

interface AuthenticationInterface
{
    /**
     * @return AuthProcedureInterface
     */
    public function getProcedure() : AuthProcedureInterface;

    /**
     * @return AuthVerifierInterface
     */
    public function getVerifier() : AuthVerifierInterface;

    /**
     * @return string
     */
    public function getUser() : string;

    /**
     * @param AuthProcedureInterface $authProcedure
     * @return AuthenticationInterface
     */
    public function setProcedure(AuthProcedureInterface $authProcedure) : AuthenticationInterface;

    /**
     * @param AuthVerifierInterface $verifier
     * @return AuthenticationInterface
     */
    public function setVerifier(AuthVerifierInterface $verifier) : AuthenticationInterface;

    /**
     * @param string $user
     * @return AuthenticationInterface
     */
    public function setUser(string $user) : AuthenticationInterface;
}