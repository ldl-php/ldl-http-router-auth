<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Auth;

use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier\AuthVerifierInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;

class Authentication implements AuthenticationInterface
{
    /**
     * @var AuthProcedureInterface
     */
    private $procedure;

    /**
     * @var AuthVerifierInterface
     */
    private $verifier;

    /**
     * @var string
     */
    private $user;

    public function getProcedure() : AuthProcedureInterface
    {
        return $this->procedure;
    }

    public function getVerifier() : AuthVerifierInterface
    {
        return $this->verifier;
    }

    public function getUser() : string
    {
        return $this->user;
    }

    public function setProcedure(AuthProcedureInterface $authProcedure) : AuthenticationInterface
    {
        $this->procedure = $authProcedure;
        return $this;
    }

    public function setVerifier(AuthVerifierInterface $verifier) : AuthenticationInterface
    {
        $this->verifier = $verifier;
        return $this;
    }

    public function setUser(string $user) : AuthenticationInterface
    {
        $this->user = $user;
        return $this;
    }
}