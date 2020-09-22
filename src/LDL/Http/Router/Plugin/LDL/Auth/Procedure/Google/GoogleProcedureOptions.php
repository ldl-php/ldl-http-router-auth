<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Google;

class GoogleProcedureOptions implements GoogleProcedureOptionsInterface
{
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
    private $userEndpoint;

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
    private $responseType;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $accessType;

    /**
     * @var string
     */
    private $includeGrantedScopes;

    /**
     * @var string
     */
    private $grantType;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $authorizationEndpoint = null,
        string $tokenEndpoint = null,
        string $userEndpoint = null,
        string $responseType = null,
        string $scope = null,
        string $accessType = null,
        string $includeGrantedScopes = null,
        string $grantType = null
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->authorizationEndpoint = $authorizationEndpoint ?? GoogleProcedureOptionsInterface::AUTHORIZATION_ENDPOINT;
        $this->tokenEndpoint = $tokenEndpoint ?? GoogleProcedureOptionsInterface::TOKEN_ENDPOINT;
        $this->userEndpoint = $userEndpoint ?? GoogleProcedureOptionsInterface::USER_INFORMATION_ENDPOINT;
        $this->responseType = $responseType ?? GoogleProcedureOptionsInterface::RESPONSE_TYPE;
        $this->scope = $scope ?? GoogleProcedureOptionsInterface::SCOPE;
        $this->accessType = $accessType ?? GoogleProcedureOptionsInterface::ACCESS_TYPE;
        $this->includeGrantedScopes = $includeGrantedScopes ?? GoogleProcedureOptionsInterface::INCLUDED_GRANTED_SCOPES;
        $this->grantType = $grantType ?? GoogleProcedureOptionsInterface::GRANT_TYPE;
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
    public function getUserEndpoint(): string
    {
        return $this->userEndpoint;
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
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getAccessType(): string
    {
        return $this->accessType;
    }

    /**
     * @return string
     */
    public function getIncludeGrantedScopes(): string
    {
        return $this->includeGrantedScopes;
    }

    /**
     * @return string
     */
    public function getGrantType(): string
    {
        return $this->grantType;
    }
}