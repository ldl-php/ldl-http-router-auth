<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Generator;

class LDLTokenGeneratorOptions
{
    private const DEFAULT_ALGORITHM = 'sha256';

    private const DEFAULT_APPLICATION = 'application';

    private const DEFAULT_HEADERS = [
        'token' => 'X-LDL-Auth-Token',
        'refresh' => 'X-LDL-Token-Refresh',
        'expiresAt' => 'X-LDL-Token-Expires'
    ];

    /**
     * @var \DateInterval
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var string
     */
    private $application;

    /**
     * @var array
     */
    private $headers;

    public function __construct(
        string $algorithm = null,
        string $expiresAt = null,
        string $application = null,
        array $headers = null
    )
    {
        $utcTZ = new \DateTimeZone("UTC");

        $this->algorithm = $algorithm ?? self::DEFAULT_ALGORITHM;
        $this->expiresAt = new \DateTime($expiresAt, $utcTZ) ?? new \DateTime("NOW", $utcTZ);
        $this->application = $application ?? self::DEFAULT_APPLICATION;
        $this->headers = $headers ?? self::DEFAULT_HEADERS;
    }

    /**
     * @return \DateInterval
     */
    public function getExpiresAt(): \DateInterval
    {
        return $this->expiresAt;
    }

    /**
     * @return string
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
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
}