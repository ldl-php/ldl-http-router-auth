<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure;

use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Exception\TypeMismatchException;

class ProcedureRepository extends ObjectCollection
{
    public function validateItem($item): void
    {
        parent::validateItem($item);

        if($item instanceof AuthProcedureInterface){
            return;
        }

        $msg = sprintf(
            'Item must implement: "%s",  instance of "%s" was given',
            AuthProcedureInterface::class,
            get_class($item)
        );

        throw new TypeMismatchException($msg);
    }

    public function getProvider(string $namespace, string $name) : ?AuthProcedureInterface
    {
        /**
         * @var AuthProcedureInterface $procedure
         */
        foreach($this as $procedure){
            if($procedure->getNamespace() === $namespace && $procedure->getName() === $name){
                return $procedure;
            }
        }

        $msg = "Authentication procedure with namespace: \"$namespace\" and name: \"$name\" could not be found!";
        throw new Exception\AuthenticationProcedureNotFound($msg);
    }

}
