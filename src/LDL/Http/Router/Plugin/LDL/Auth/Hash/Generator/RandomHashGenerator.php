<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator;

class RandomHashGenerator implements RandomHashGeneratorInterface
{
    /**
     * @var RandomHashGeneratorOptionsInterface
     */
    private $options;

    public function __construct(
        RandomHashGeneratorOptionsInterface $options=null
    )
    {
        $this->options = $options ?? new RandomHashGeneratorOptions();
    }

    public function generate() : string
    {
        $hashAlgorithms = \hash_algos();
        $algorithm = strtolower(trim($this->options->getAlgorithm()));

        if(!in_array($algorithm, $hashAlgorithms, true)) {
            $msg = sprintf(
              'Invalid hashing algorithm: "%s", valid algorithms are: "%s"',
              $algorithm,
              implode(',', $hashAlgorithms)
            );

            throw new \InvalidArgumentException($msg);
        }

        return \hash(
            $algorithm,
            \random_bytes($this->options->getRandomDataLen()),
            $this->options->isBinary()
        );
    }
}