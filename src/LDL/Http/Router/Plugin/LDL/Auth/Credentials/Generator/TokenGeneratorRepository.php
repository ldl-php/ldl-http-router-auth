<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Exception\TypeMismatchException;

class TokenGeneratorRepository extends ObjectCollection
{
    public function validateItem($item): void
    {
        parent::validateItem($item);

        if($item instanceof TokenGeneratorInterface){
            return;
        }

        $msg = sprintf(
            'Item must implement: "%s", instance of "%s" was given',
            TokenGeneratorInterface::class,
            get_class($item)
        );

        throw new TypeMismatchException($msg);
    }

    public function getDefault() : ?TokenGeneratorInterface
    {

    }

    public function getGenerator(string $namespace, string $name) : ?TokenGeneratorInterface
    {
        /**
         * @var TokenGeneratorInterface $procedure
         */
        foreach($this as $generator){
            if($generator->getNamespace() === $namespace && $generator->getName() === $name){
                return $generator;
            }
        }

        $msg = "Token generator with namespace: \"$namespace\" and name: \"$name\" could not be found!";
        throw new Exception\TokenGeneratorNotFound($msg);
    }

}
