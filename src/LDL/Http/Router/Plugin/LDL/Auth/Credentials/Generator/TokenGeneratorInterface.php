<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

use LDL\Http\Core\Response\ResponseInterface;

interface TokenGeneratorInterface
{
    public function getNamespace() : string;

    public function getName() : string;

    /**
     * Must set corresponding token headers
     * @param array $user
     * @param ResponseInterface $response
     * @return string
     */
    public function create(array $user, ResponseInterface $response) : string;

    public function destroy(array $user, ResponseInterface $response) : bool;
}