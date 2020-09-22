<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook;

class FacebookProcedureOptions implements FacebookProcedureOptionsInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $graphDomain;

    /**
     * @var string
     */
    private $authorizationEndpoint;

    /**
     * @var string
     */
    private $tokenEndpoint;

    /**
     * @var string
     */
    private $resourceAccessEndpoint;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $tokenKey;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var string
     */
    private $userFields;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $domain = null,
        string $graphDomain = null,
        string $authorizationEndpoint = null,
        string $tokenEndpoint = null,
        string $resourceAccessEndpoint = null,
        string $version = null,
        string $tokenKey = null,
        string $userFields = null
    )
    {
        $this->clientId = $clientId ?? FacebookProcedureOptionsInterface::CLIENT_ID;
        $this->clientSecret = $clientSecret ?? FacebookProcedureOptionsInterface::CLIENT_SECRET;
        $this->redirectUri = $redirectUri;
        $this->domain = $domain ?? FacebookProcedureOptionsInterface::DEFAULT_DOMAIN;
        $this->graphDomain = $graphDomain ?? FacebookProcedureOptionsInterface::DEFAULT_GRAPH_DOMAIN;
        $this->authorizationEndpoint = $authorizationEndpoint ?? FacebookProcedureOptionsInterface::AUTHORIZATION_ENDPOINT;
        $this->tokenEndpoint = $tokenEndpoint ?? FacebookProcedureOptionsInterface::TOKEN_ENDPOINT;
        $this->resourceAccessEndpoint = $resourceAccessEndpoint ?? FacebookProcedureOptionsInterface::RESOURCE_ACCESS_ENDPOINT;
        $this->version = $version ?? FacebookProcedureOptionsInterface::DEFAULT_VERSION;
        $this->tokenKey = $tokenKey ?? FacebookProcedureOptionsInterface::TOKEN_KEY;
        $this->userFields = $userFields ?? FacebookProcedureOptionsInterface::DEFAULT_USER_FIELDS;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getGraphDomain(): string
    {
        return $this->graphDomain;
    }

    /**
     * @return string
     */
    public function getAuthorizationEndpoint(): string
    {
        return $this->authorizationEndpoint;
    }

    /**
     * @return string
     */
    public function getTokenEndpoint(): string
    {
        return $this->tokenEndpoint;
    }

    /**
     * @return string
     */
    public function getResourceAccessEndpoint(): string
    {
        return $this->resourceAccessEndpoint;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getTokenKey(): string
    {
        return $this->tokenKey;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function getUserFields(): string
    {
        return $this->userFields;
    }
}