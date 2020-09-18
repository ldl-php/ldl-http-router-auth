<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

interface PlainFileCredentialsProviderOptionsInterface
{
    /**
     * @return int|null
     */
    public function getCipher() : ?int;

    /**
     * @return array
     */
    public function getOptions() : array;

    /**
     * @return string
     */
    public function getSeparator() : string;
}