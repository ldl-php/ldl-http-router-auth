<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider;

interface CredentialsProviderInterface
{
    /**
     *
     * Return an array in case credentials are found
     * Return null on credentials not found / invalid
     *
     * @param mixed ...$args
     * @return array|null
     */
    public function fetch(...$args) : ?array;
}