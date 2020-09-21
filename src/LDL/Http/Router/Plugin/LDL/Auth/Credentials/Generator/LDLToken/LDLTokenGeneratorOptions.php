<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

class LDLTokenGeneratorOptions
{
    /**
     * @var \DateInterval
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $algorithm;

    public function __construct(
        string $algorithm,
        string $expiresAt
    )
    {
    }


}