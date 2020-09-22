<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Hash\Generator;

interface RandomHashGeneratorOptionsInterface
{

    public function getAlgorithm() : string;

    public function isBinary() : bool;

    public function getRandomDataLen() : int;
}