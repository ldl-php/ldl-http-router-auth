<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Facebook;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use LDL\Http\Core\Request\RequestInterface;
use LDL\Http\Core\Response\ResponseInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Credentials\Provider\CredentialsProviderInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationFailureException;
use LDL\Http\Router\Plugin\LDL\Auth\Dispatcher\Exception\AuthenticationRequiredException;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\AbstractAuthProcedure;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\RequestKeyInterface;
use LDL\Http\Router\Plugin\LDL\Auth\Procedure\UserDataInterface;

class FacebookProcedure extends AbstractAuthProcedure implements RequestKeyInterface, UserDataInterface
{
    public const NAMESPACE = 'FacebookPlugin';
    public const NAME = 'Credentials';
    public const DESCRIPTION = 'Provides an authentication based on Facebook oauth';

    /**
     * @var FacebookProcedureOptionsInterface
     */
    private $options;

    /**
     * @var array;
     */
    private $userData;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(
        CredentialsProviderInterface $provider,
        FacebookProcedureOptionsInterface $options,
        HttpClientInterface $httpClient = null,
        bool $isDefault = false
    )
    {
        $this->httpClient = $httpClient ?? new HttpClient();
        $this->options = $options;

        $this->setCredentialsProvider($provider)
            ->setDefault($isDefault)
            ->setNamespace(self::NAMESPACE)
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
    }

    public function getAuthorizationEndpoint() : string
    {
        $endpoint = sprintf(
            '%s/%s/%s',
            $this->options->getDomain(),
            $this->options->getVersion(),
            $this->options->getAuthorizationEndpoint()
        );

        return sprintf(
            '%s?%s',
            $endpoint,
            "client_id={$this->options->getClientId()}&redirect_uri={$this->options->getRedirectUri()}&scope=email"
        );
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        $accessToken = $this->getTokenEndpointForCode($request->get('code'));

        if(empty($accessToken)){
            return null;
        }

        $user = $this->getUserInformation($accessToken['response']['access_token']);

        if(!array_key_exists('email', $user['response'])){
            throw new AuthenticationFailureException('Email required');
        }

        $this->userData = $user;

        return $user['response']['email'];
    }

    public function getUserData(): ?array
    {
        return $this->userData;
    }

    public function handle(
        RequestInterface $request,
        ResponseInterface $response
    ) : void
    {
        if(!$request->get('code')){
            throw new AuthenticationRequiredException('Authentication required');
        }
    }

    //<editor-fold desc="Private methods">
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

    private function request(string $endpoint, array $params)
    {
        $response = $this->httpClient->request(RequestInterface::HTTP_METHOD_GET, $endpoint . '?' . http_build_query($params));
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
    //</editor-fold>
}