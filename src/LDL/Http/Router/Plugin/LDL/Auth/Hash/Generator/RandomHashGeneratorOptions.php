<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator;

class RandomHashGeneratorOptions implements RandomHashGeneratorOptionsInterface
{
    public const ALGORITHM_DEFAULT = 'sha3-512';

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var int
     */
    private $randomDataLen;

    /**
     * @var bool
     */
    private $binaryOutput;

    public function __construct(
        string $algorithm=null,
        int $randomDataLen=128,
        bool $binaryOutput=false
    )
    {
        $this->algorithm = $algorithm ?? self::ALGORITHM_DEFAULT;
        $this->binaryOutput = $binaryOutput;
        $this->randomDataLen = $randomDataLen;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function isBinary() : bool
    {
        return $this->binaryOutput;
    }

    public function getRandomDataLen() : int
    {
        return $this->randomDataLen;
    }
}