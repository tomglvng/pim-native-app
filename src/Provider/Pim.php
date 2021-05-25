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
class Pim extends AbstractProvider
{
    const PIM_DOMAIN = 'http://localhost:8080';

    public function getBaseAuthorizationUrl()
    {
        return sprintf('%s/login/oauth2/authorize', self::PIM_DOMAIN);
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return sprintf('%s/login/oauth2/access_token', self::PIM_DOMAIN);
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
