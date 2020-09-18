<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\File\Plain;

class PlainFileCredentialsProviderOptions implements PlainFileCredentialsProviderOptionsInterface
{
    public const SEPARATOR_DEFAULT = ':';
    public const CIPHER_DEFAULT = 'PLAIN_TEXT';
    public const CIPHER_OPTIONS_DEFAULT = [];

    /**
     * @var int|string
     */
    private $cipher;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        $separator = null,
        $cipher=null,
        $options=[]
    )
    {

        $this->separator = $separator ?? self::SEPARATOR_DEFAULT;
        $this->cipher = $cipher ?? self::CIPHER_DEFAULT;
        $this->options = $options ?? self::CIPHER_OPTIONS_DEFAULT;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCipher() :?int
    {
        return $this->cipher;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

}