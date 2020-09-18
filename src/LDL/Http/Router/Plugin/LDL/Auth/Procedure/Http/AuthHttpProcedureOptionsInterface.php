<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http;

use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureOptionsInterface;

interface AuthHttpProcedureOptionsInterface extends AuthProcedureOptionsInterface
{

    /**
     * @return string
     */
    public function getRealm() : string;

    /**
     * @return string
     */
    public function getType() : string;

}