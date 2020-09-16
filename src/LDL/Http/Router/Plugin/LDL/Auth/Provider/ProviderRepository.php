<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Provider;

use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Exception\TypeMismatchException;

class ProviderRepository extends ObjectCollection
{
    public function validateItem($item): void
    {
        parent::validateItem($item);

        if(
            ($item instanceof AuthCredentialsProviderInterface || $item instanceof AuthTokenProviderInterface) &&
            $item instanceof AuthenticationProviderInterface
        ){
            return;
        }

        $msg = sprintf(
            'Item must implement: "%s", plus one of "%s" or "%s" (or both), instance of "%s" was given',
            AuthenticationProviderInterface::class,
            AuthCredentialsProviderInterface::class,
            AuthTokenProviderInterface::class,
            get_class($item)
        );

        throw new TypeMismatchException($msg);
    }

    public function getProvider(string $namespace, string $name) : ?AuthenticationProviderInterface
    {
        /**
         * @var AuthenticationProviderInterface $provider
         */
        foreach($this as $provider){
            if($provider->getNamespace() === $namespace && $provider->getName() === $name){
                return $provider;
            }
        }

        $msg = "Authentication provider with namespace: \"$namespace\" and name: \"$name\" could not be found!";
        throw new Exception\AuthenticationProviderNotFound($msg);
    }

}
