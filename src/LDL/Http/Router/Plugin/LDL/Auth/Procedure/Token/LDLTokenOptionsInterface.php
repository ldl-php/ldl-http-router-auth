<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Token;

use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureOptionsInterface;

interface LDLTokenOptionsInterface extends AuthProcedureOptionsInterface
{
    /**
     * @return string
     */
    public function getKey() : string;

    /**
     * @return bool
     */
    public function isFromHeaders() : ?bool;
}