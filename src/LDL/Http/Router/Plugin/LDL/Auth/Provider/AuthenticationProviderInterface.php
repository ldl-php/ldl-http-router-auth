<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Provider;

interface AuthenticationProviderInterface
{
    /**
     * @return string
     */
    public function getNamespace() : string;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @return mixed
     */
    public function getDescription() : string;
}