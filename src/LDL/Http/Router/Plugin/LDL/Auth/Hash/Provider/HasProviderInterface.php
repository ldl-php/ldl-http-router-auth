<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider;

interface HasProviderInterface
{
    /**
     * @param string $password
     * @return bool|string
     */
    public function hash(string $password);

    /**
     * @param string $text
     * @param string $password
     * @return bool
     */
    public function compare(string $text, string $password) : bool;

    /**
     * @return CipherProviderOptionsInterface
     */
    public function getOptions() : CipherProviderOptionsInterface;
}