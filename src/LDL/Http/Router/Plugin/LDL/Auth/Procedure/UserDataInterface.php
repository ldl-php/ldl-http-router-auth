<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

interface UserDataInterface
{
    /**
     * @return array
     */
    public function getUserData() : ?array;
}