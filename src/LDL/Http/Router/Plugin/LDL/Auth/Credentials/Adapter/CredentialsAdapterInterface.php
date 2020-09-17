<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Adapter;

interface CredentialsAdapterInterface
{
    /**
     * @param mixed ...$args
     * @return array|null
     */
    public function isAuthenticated(...$args) : ?array;
}