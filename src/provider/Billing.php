<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Helpers\CodeChallenge;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Billing extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Domain
     *
     * @var string
     */
    public $domain = 'https://localhost:5001';

    /**
     * Api domain
     *
     * @var string
     */
    public $apiDomain = 'https://thullner-billing-api.azurewebsites.net';

    /** @var CodeChallenge */
    private $codeChallenge;

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->domain . '/connect/authorize';
    }


    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain . '/connect/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiDomain . '/api/users/testuser';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ['openid'];
    }

    protected function getAuthorizationParameters(array $options)
    {
        $options = parent::getAuthorizationParameters($options);

        $this->codeChallenge = new CodeChallenge();
        $this->codeChallenge->generate();

        $options['code_challenge'] = $this->codeChallenge->challenge;
        $options['code_challenge_method'] = $this->codeChallenge->challengeMethod;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['code_verifier'] = $this->codeChallenge->verifier;


        return $options;
    }

    public function getAccessToken($grant, array $options = [])
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $options['code_verifier'] = $_SESSION['code_verifier'];

        unset($_SESSION['code_verifier']);

        $options['response_type'] = 'code';

        return parent::getAccessToken($grant, $options);
    }

    /**
     * Check a provider response for errors.
     *
     * @link   https://developer.github.com/v3/#client-errors
     * @link   https://developer.github.com/v3/oauth/#common-errors-for-the-access-token-request
     * @throws IdentityProviderException
     * @param ResponseInterface $response
     * @param array $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
//        if ($response->getStatusCode() >= 400) {
//            throw new Exception($response, $data);
//        } elseif (isset($data['error'])) {
//            throw new Exception($response, $data);
//        }

        return true;
    }


    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new BillingUser($response);

        return $user->setDomain($this->domain);
    }
}
