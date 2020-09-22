<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Verifier;

use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Exception\TypeMismatchException;

class AuthVerifierRepository extends ObjectCollection
{
    public function validateItem($item): void
    {
        parent::validateItem($item);

        if($item instanceof AuthVerifierInterface){
            return;
        }

        $msg = sprintf(
            'Item must implement: "%s", instance of "%s" was given',
            AuthVerifierInterface::class,
            get_class($item)
        );

        throw new TypeMismatchException($msg);
    }

    public function getVerifier(string $namespace, string $name) : ?AuthVerifierInterface
    {
        /**
         * @var AuthVerifierInterface $procedure
         */
        foreach($this as $verifier){
            if($verifier->getNamespace() === $namespace && $verifier->getName() === $name){
                return $verifier;
            }
        }

        $msg = "Authentication verifier with namespace: \"$namespace\" and name: \"$name\" could not be found!";
        throw new Exception\AuthVerifierNotFound($msg);
    }

}
