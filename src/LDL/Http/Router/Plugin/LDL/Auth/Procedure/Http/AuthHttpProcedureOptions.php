<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Http;

class AuthHttpProcedureOptions implements AuthHttpProcedureOptionsInterface
{
    public const REALM_DEFAULT =  'Authentication required';
    public const TYPE_DEFAULT = 'Basic';

    /**
     * @var string
     */
    private $realm;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        string $realm = null,
        string $type = null
    )
    {
        $this->realm = $realm ?? self::REALM_DEFAULT;
        $this->type = $type ?? self::TYPE_DEFAULT;
    }

    public function getRealm() : string
    {
        return $this->realm;
    }

    public function getType() : string
    {
        return $this->type;
    }

}

