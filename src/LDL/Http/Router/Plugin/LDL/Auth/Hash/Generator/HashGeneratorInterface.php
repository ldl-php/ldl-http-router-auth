<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator;

interface HashGeneratorInterface
{
    public function generate(string $algorithm) : string;
}