<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderInterface;

interface PlainFileCredentialsProviderOptionsInterface
{
    public const SEPARATOR_DEFAULT = ':';

    /**
     * @return CipherProviderInterface
     */
    public function getCipherProvider() :CipherProviderInterface;

    /**
     * @return string
     */
    public function getSeparator(): string;
}