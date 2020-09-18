<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials;

use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureOptionsInterface;

interface AuthCredentialsOptionsInterface extends AuthProcedureOptionsInterface
{
    /**
     * @return string
     */
    public function getKey() : string;

    /**
     * @return string
     */
    public function getSecret() : string;
}