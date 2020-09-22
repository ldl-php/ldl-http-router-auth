<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator\Token\LDLToken;

class LDLTokenGeneratorOptions
{
    private const DEFAULT_APPLICATION = 'application';

    private const DEFAULT_HEADERS = [
        'token' => 'X-LDL-Auth-Token',
        'refresh' => 'X-LDL-Token-Refresh',
        'expiresAt' => 'X-LDL-Token-Expires'
    ];

    /**
     * @var string
     */
    private $refreshEndpoint;

    /**
     * @var string
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $application;

    /**
     * @var array
     */
    private $headers;

    public function __construct(
        string $refreshEndpoint=null,
        string $expiresAt = null,
        string $application = null,
        array $headers = null
    )
    {
        $this->refreshEndpoint = $refreshEndpoint;
        $this->application = $application ?? self::DEFAULT_APPLICATION;
        $this->headers = $headers ?? self::DEFAULT_HEADERS;
    }

    /**
     * @return \DateInterval
     */
    public function getExpiresAt(): \DateInterval
    {
        return \DateInterval::createFromDateString($this->expiresAt ?? '+30 minutes');
    }

    /**
     * @return string
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRefreshEndpoint() : string
    {
        return $this->refreshEndpoint;
    }
}