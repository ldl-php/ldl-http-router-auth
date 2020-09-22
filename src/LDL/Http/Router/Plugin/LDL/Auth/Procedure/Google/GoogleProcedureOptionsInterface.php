<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Google;

interface GoogleProcedureOptionsInterface
{
    public const AUTHORIZATION_ENDPOINT = 'https://accounts.google.com/o/oauth2/v2/auth';
    public const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    public const USER_INFORMATION_ENDPOINT = 'https://www.googleapis.com/oauth2/v3/userinfo';
    public const ACCESS_TOKEN_KEY = 'access_token';
    public const REFRESH_TOKEN_KEY = 'refresh_token';
    public const SCOPE = 'openid%20email';
    public const ACCESS_TYPE = 'offline';
    public const INCLUDED_GRANTED_SCOPES = 'true';
    public const GRANT_TYPE = 'authorization_code';
    public const RESPONSE_TYPE = 'code';

    /**
     * @return string
     */
    public function getAuthorizationEndpoint(): string;

    /**
     * @return string
     */
    public function getTokenEndpoint(): string;

    /**
     * @return string
     */
    public function getUserEndpoint(): string;

    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @return string
     */
    public function getRedirectUri(): string;

    /**
     * @return string
     */
    public function getResponseType(): string;

    /**
     * @return string
     */
    public function getScope(): string;

    /**
     * @return string
     */
    public function getAccessType(): string;

    /**
     * @return string
     */
    public function getIncludeGrantedScopes(): string;

    /**
     * @return string
     */
    public function getGrantType(): string;
}