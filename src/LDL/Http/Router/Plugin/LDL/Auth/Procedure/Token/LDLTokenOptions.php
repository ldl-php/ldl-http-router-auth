<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Credentials\Token;

use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Token\LDLTokenOptionsInterface;

class LDLTokenOptions implements LDLTokenOptionsInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var bool
     */
    private $fromHeaders;

    public function __construct(
        string $key,
        bool $fromHeaders = null
    )
    {
        $this->key = $key;
        $this->fromHeaders = $fromHeaders;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function isFromHeaders() : ?bool
    {
        return $this->fromHeaders;
    }
}