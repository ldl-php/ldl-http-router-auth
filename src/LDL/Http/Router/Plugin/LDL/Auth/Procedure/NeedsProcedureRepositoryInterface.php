<?php declare(strict_types=1);

/**
 * This interface is only recommended to be used if you are not using a dependency injection container
 */
namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

interface NeedsProcedureRepositoryInterface
{
    public function setProcedureRepository(ProcedureRepository $repository);
}