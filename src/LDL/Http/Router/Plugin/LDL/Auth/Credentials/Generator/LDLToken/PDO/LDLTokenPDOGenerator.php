<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\LDLTokenGeneratorOptions;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator\HashGeneratorInterface;

class LDLTokenPDOGenerator implements TokenGeneratorInterface
{
    private const NAMESPACE = 'LDLAuthPlugin';
    private const NAME = 'LDLTokenPDOGenerator';

    private const DEFAULT_TABLE = 'ldl_auth_token';
    private const DEFAULT_ENDPOINT = '/token/refresh';

    /**
     * @var LDLTokenGeneratorOptions
     */
    private $options;

    /**
     * @var HashGeneratorInterface
     */
    private $hashGenerator;

    public function __construct(HashGeneratorInterface $hashGenerator, LDLTokenGeneratorOptions $options = null)
    {
        $this->hashGenerator = $hashGenerator;
        $this->options = $options ?? new LDLTokenGeneratorOptions();
    }

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function create(ResponseInterface $response) : string
    {
        $token = $this->hashGenerator->generate($this->options->getAlgorithm());
        $headers = $this->options->getHeaders();

        $response->getHeaderBag()->set($headers['token'], $token);
        $response->getHeaderBag()->set($headers['refresh'], self::DEFAULT_ENDPOINT);
        $response->getHeaderBag()->set($headers['expiresAt'], $this->options->getExpiresAt());

        return $token;
    }

    public function destroy(ResponseInterface $response) : bool
    {

    }

    public function updateOptions(LDLTokenGeneratorOptions $options) : TokenGeneratorInterface
    {
        $this->options = $options;

        return $this;
    }
}