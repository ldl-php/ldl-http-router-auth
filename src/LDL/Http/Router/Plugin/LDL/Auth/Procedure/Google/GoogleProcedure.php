<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Auth\Procedure\Google;

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

class GoogleProcedure extends AbstractAuthProcedure implements RequestKeyInterface, UserDataInterface
{
    public const NAMESPACE = 'GooglePlugin';
    public const NAME = 'Credentials';
    public const DESCRIPTION = 'Provides an authentication based on Google oauth';

    /**
     * @var GoogleProcedureOptionsInterface
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
        GoogleProcedureOptionsInterface $options,
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
        $endpoint = $this->options->getAuthorizationEndpoint();

        return sprintf(
            '%s?%s&%s&%s&%s&%s&%s',
            $endpoint,
            "scope={$this->options->getScope()}",
            "access_type={$this->options->getAccessType()}",
            "include_granted_scopes={$this->options->getIncludeGrantedScopes()}",
            "response_type={$this->options->getResponseType()}",
            "redirect_uri={$this->options->getRedirectUri()}",
            "client_id={$this->options->getClientId()}"
        );
    }

    public function getKeyFromRequest(RequestInterface $request): ?string
    {
        if($request->get('error')){
            throw new AuthenticationFailureException($request->get('error'));
        }

        $accessToken = $this->getTokenEndpointForCode($request->get('code'));

        if(empty($accessToken)){
            return null;
        }

        $user = $this->getUserInformation($accessToken['response'][GoogleProcedureOptionsInterface::ACCESS_TOKEN_KEY]);

        if(!array_key_exists('email', $user['response'])){
            throw new AuthenticationFailureException('Email required');
        }

        if(false === $user['response']['email_verified']){
            throw new AuthenticationFailureException('Email is not verified');
        }

        $this->userData = $user['response'];

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
        $endpoint = $this->options->getTokenEndpoint();

        $params = [
            'code' => $code,
            'client_id' => $this->options->getClientId(),
            'client_secret' => $this->options->getClientSecret(),
            'redirect_uri' => $this->options->getRedirectUri(),
            'grant_type' => $this->options->getGrantType()
        ];

        return $this->request($endpoint, $params, RequestInterface::HTTP_METHOD_POST);
    }

    private function getUserInformation(string $accessToken) : array
    {
        $endpoint = $this->options->getUserEndpoint();

        $params = [
            'access_token' => $accessToken
        ];

        return $this->request($endpoint, $params, RequestInterface::HTTP_METHOD_POST);
    }

    private function request(string $endpoint, array $params, string $httpMethod)
    {
        $response = $this->httpClient->request($httpMethod, $endpoint . '?' . http_build_query($params));
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