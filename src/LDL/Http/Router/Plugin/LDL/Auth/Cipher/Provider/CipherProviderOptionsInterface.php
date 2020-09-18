<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider;

interface CipherProviderOptionsInterface
{
    public const PLAIN_TEXT = -237;

    public function getCipher();

    /**
     * @return array
     */
    public function getParameters(): array;
}