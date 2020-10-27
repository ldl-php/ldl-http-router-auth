<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Credentials\Client;

use GuzzleHttp\Client;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook\FacebookClientOptionsInterface;
use Psr\Http\Client\ClientInterface;

class FacebookClient implements FacebookClientInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $httpClient;

    /**
     * @var FacebookClientOptionsInterface
     */
    private $options;

    /**
     * @var array
     */
    private $userData;

    /**
     * @var array
     */
    private $staticUserData;

    public function __construct(
        FacebookClientOptionsInterface $options,
        ClientInterface $httpClient = null
    )
    {
        $this->httpClient = $httpClient ?? new Client();
        $this->options = $options;
    }

    public static function getStaticUserData() : array
    {

    }

    public function getUserData(string $code) : ?array
    {
        if(null !== $this->userData){
            return $this->userData;
        }

        $accessToken = $this->getTokenEndpointForCode($code);

        if(empty($accessToken)){
            return null;
        }

        $user = $this->getUserInformation($accessToken['response']['access_token']);

        $this->userData = $user;

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint(array $scope=['email']) : string
    {
        $endpoint = sprintf(
            '%s/%s/%s',
            $this->options->getDomain(),
            $this->options->getVersion(),
            $this->options->getAuthorizationEndpoint()
        );

        return sprintf(
            '%s?client_id=%s&redirect_uri=%s&scope=%s',
            $endpoint,
            $this->options->getClientId(),
            $this->options->getRedirectUri(),
            implode(',', $scope)
        );
    }

    private function getTokenEndpointForCode(string $code) : array
    {
        $endpoint = sprintf(
            '%s/%s/%s',
            $this->options->getGraphDomain(),
            $this->options->getVersion(),
            $this->options->getTokenEndpoint()
        );

        $params = [
            'redirect_uri' => $this->options->getRedirectUri(),
            'client_id' => $this->options->getClientId(),
            'client_secret' => $this->options->getClientSecret(),
            'code' => $code
        ];

        return $this->request($endpoint, $params);
    }

    private function getUserInformation(string $accessToken) : array
    {
        $endpoint = sprintf(
            '%s/%s',
            $this->options->getGraphDomain(),
            $this->options->getResourceAccessEndpoint()
        );

        $params = [
            'fields' => $this->options->getUserFields(),
            'access_token' => $accessToken
        ];

        return $this->request($endpoint, $params);
    }

    private function request(string $endpoint, array $params) : array
    {
        $response = $this->httpClient->request(
            RequestInterface::HTTP_METHOD_GET,
            $endpoint . '?' . http_build_query($params)
        );

        $data = json_decode((string) $response->getBody(), true, 512, \JSON_THROW_ON_ERROR);
        $success = !array_key_exists('error', $data) ? true : false;

        $return = [
            'success' => $success,
            'response' => $data
        ];

        if(!$success){
            $return['message'] = $response['error']['message'];
        }

        return $return;
    }

}
