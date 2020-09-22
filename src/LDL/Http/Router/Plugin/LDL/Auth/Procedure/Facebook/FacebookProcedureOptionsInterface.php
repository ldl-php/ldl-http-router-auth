<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook;

interface FacebookProcedureOptionsInterface
{
    public const DEFAULT_DOMAIN = 'https://www.facebook.com';
    public const DEFAULT_GRAPH_DOMAIN = 'https://graph.facebook.com';
    public const AUTHORIZATION_ENDPOINT = 'dialog/oauth';
    public const TOKEN_ENDPOINT = 'oauth/access_token';
    public const RESOURCE_ACCESS_ENDPOINT = 'me';
    public const DEFAULT_VERSION = 'v8.0';
    public const TOKEN_KEY = 'access_token';
    public const CLIENT_ID = '';
    public const CLIENT_SECRET = '';
    public const DEFAULT_USER_FIELDS = 'first_name,last_name,email,picture';

    /**
     * @return string
     */
    public function getDomain(): string;

    /**
     * @return string
     */
    public function getGraphDomain(): string;

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
    public function getResourceAccessEndpoint(): string;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @return string
     */
    public function getTokenKey(): string;

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
     * This must be a comma separated string with facebook user fields
     *
     * @return string
     */
    public function getUserFields(): string;
}