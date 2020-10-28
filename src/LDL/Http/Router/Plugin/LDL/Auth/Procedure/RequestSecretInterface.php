<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Core\Request\RequestInterface;

interface RequestSecretInterface
{
    /**
     * @param RequestInterface $request
     * @return string|null
     */
    public function getSecretFromRequest(RequestInterface $request) : ?string;
}