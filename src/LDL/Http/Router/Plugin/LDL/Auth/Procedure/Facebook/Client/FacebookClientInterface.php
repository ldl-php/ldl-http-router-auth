<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Client;

interface FacebookClientInterface
{
    /**
     * Retrieves user data from facebook
     *
     * @param string $code
     * @return array|null
     */
    public function getUserData(string $code) : ?array;

    /**
     * This is the login endpoint that you would normally use to set on any templat
     * @param array $scope
     * @return string
     */
    public function getAuthorizationEndpoint(array $scope=['email']) : string;

}