<?php

namespace App\Controller;

use App\Provider\PimProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NativeAppController extends AbstractController
{
    private string $clientId;
    private string $clientSecret;
    private string $appActivateUrl;

    public function __construct(string $clientId, string $clientSecret, string $appUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->appActivateUrl = sprintf('%s/activate', $appUrl);
    }

    #[Route('/activate', name: 'activate')]
    public function activate(
        Request $request
    ): Response {
        // TODO: REDIRECT (https://github.com/thephpleague/oauth2-github)
        //dd($request);

        $code = $request->get('code');
        $state = $request->get('state');
        $session = $request->getSession();

        if ($request->get('pim')) {
            $session->set('pimurl', $request->get('pim'));
        }

        $provider = new PimProvider(
            $session->get('pimurl'), [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => $this->appActivateUrl,
        ]
        );


        if (null === $code) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $session->set('oauth2state', $provider->getState());

            header('Location: '.$authUrl);
            exit;
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($state) || ($state !== $session->get('oauth2state'))) {
            $session->remove('oauth2state');
            exit('Invalid state');
        }

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken(
            'authorization_code',
            [
                'code' => $code,
            ]
        );

        // Optional: Now you have a token you can look up a users profile data
        /*try {

            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);
        } catch (\Exception $e) {

            // Failed to get user details
            exit('Oh dear...');
        }*/

        // Use this to interact with an API on the users behalf
        //echo $token->getToken();

        return new JsonResponse(['token' => $token->getToken()]);
    }
}
