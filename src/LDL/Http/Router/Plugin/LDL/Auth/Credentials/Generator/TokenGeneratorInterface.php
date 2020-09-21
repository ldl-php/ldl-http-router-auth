<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

use LDL\Http\Core\Response\ResponseInterface;

interface TokenGeneratorInterface
{
    public function getNamespace() : string;

    public function getName() : string;

    public function create(ResponseInterface $response) : string;

    public function destroy(ResponseInterface $response) : bool;

    public function updateOptions(LDLTokenGeneratorOptions $options) : TokenGeneratorInterface;
}