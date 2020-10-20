<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook;

use LDL\Http\Router\Handler\Exception\AbstractExceptionHandler;
use LDL\Http\Router\Handler\Exception\ModifiesResponseInterface;
use LDL\Http\Router\Router;

class FacebookCodeExceptionHandler extends AbstractExceptionHandler implements ModifiesResponseInterface
{
    public function getContent(): array
    {
        return [
            'ksjdklasd' => 1
        ];
    }

    public function handle(Router $router, \Exception $e, string $context): ?int
    {
        return null;
    }
}