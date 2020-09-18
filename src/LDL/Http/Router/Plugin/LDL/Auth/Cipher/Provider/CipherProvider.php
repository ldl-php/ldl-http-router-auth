<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Cipher\Provider;

class CipherProvider implements CipherProviderInterface
{
    /**
     * @var CipherProviderOptionsInterface
     */
    private $options;

    public function __construct(CipherProviderOptionsInterface $options)
    {
        $this->options = $options;
    }

    public function hash(string $password)
    {
        return password_hash($password, $this->options->getCipher(), $this->options->getParameters());
    }

    public function compare(string $text, string $password) : bool
    {
        return $this->options->getCipher() === CipherProviderOptionsInterface::PLAIN_TEXT ? $text === $password : \password_verify($text, $password);
    }

    public function getOptions() : CipherProviderOptionsInterface
    {
        return $this->options;
    }
}