<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProvider;
use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider\CipherProviderOptionsInterface;

class PlainFileCredentialsProviderOptions implements PlainFileCredentialsProviderOptionsInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @var CipherProviderInterface
     */
    private $cipherProvider;

    public function __construct(
        $separator = null,
        CipherProviderInterface $cipherProvider = null
    )
    {
        $this->separator = $separator ?? PlainFileCredentialsProviderOptionsInterface::SEPARATOR_DEFAULT;
        $this->cipherProvider = $cipherProvider ?? new CipherProvider(new CipherProviderOptions(CipherProviderOptionsInterface::PLAIN_TEXT));
    }

    public function getCipherProvider() :CipherProviderInterface
    {
        return $this->cipherProvider;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

}