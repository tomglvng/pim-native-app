<?php

namespace App\Provider;

use App\Exception\PimProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimProvider extends AbstractProvider
{
    private string $pimUrl;

    public function __construct( string $pimUrl, array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->pimUrl = $pimUrl;
    }

    public function setPimUrl(string $pimUrl)
    {
        $this->pimUrl = $pimUrl;
    }

    public function getBaseAuthorizationUrl()
    {
        return sprintf('%s/login/oauth2/authorize', $this->pimUrl);
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return sprintf('%s/login/oauth2/access_token', $this->pimUrl);
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return '';
    }

    protected function getDefaultScopes()
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw PimProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw PimProviderException::oauthException($response, $data);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new PimResourceOwner($response);
    }
}
