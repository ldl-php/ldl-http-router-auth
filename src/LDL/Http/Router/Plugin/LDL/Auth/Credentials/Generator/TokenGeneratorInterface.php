<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AuthProcedureInterface;

interface TokenGeneratorInterface
{
    public function getNamespace() : string;

    public function getName() : string;

    /**
     * Must set corresponding token headers
     * @param array $user
     * @param ResponseInterface $response
     * @param AuthProcedureInterface $authProcedure
     * @return string
     */
    public function create(array $user, ResponseInterface $response, AuthProcedureInterface $authProcedure) : string;

    public function destroy(array $user, ResponseInterface $response, AuthProcedureInterface $authProcedure) : bool;
}