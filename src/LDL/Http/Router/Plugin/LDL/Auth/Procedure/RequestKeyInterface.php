<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Http\Core\Request\RequestInterface;

interface RequestKeyInterface
{
    /**
     * @param RequestInterface $request
     * @return string|null
     */
    public function getKeyFromRequest(RequestInterface $request) : ?string;
}