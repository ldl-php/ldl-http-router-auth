<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

use LDL\Type\Collection\Interfaces\Namespaceable\NamespaceableInterface;
use LDL\Type\Collection\Traits\Namespaceable\NamespaceableTrait;
use LDL\Type\Collection\Types\Object\ObjectCollection;
use LDL\Type\Collection\Types\Object\Validator\InterfaceComplianceItemValidator;

class TokenGeneratorRepository extends ObjectCollection implements NamespaceableInterface
{
    use NamespaceableTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);
        $this->getValidatorChain()
            ->append(new InterfaceComplianceItemValidator(TokenGeneratorInterface::class))
            ->lock();
    }

    public function getDefault() : ?TokenGeneratorInterface
    {

    }

}
