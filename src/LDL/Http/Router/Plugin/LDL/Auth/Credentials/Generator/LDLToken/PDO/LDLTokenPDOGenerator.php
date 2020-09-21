<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\TokenGeneratorInterface;

class LDLTokenPDOGenerator implements TokenGeneratorInterface
{
    public function create(ResponseInterface $response) : string
    {

    }

    public function destroy(ResponseInterface $response) : bool
    {

    }
}