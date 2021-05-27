<?php

namespace App\Controller;

use App\Provider\PimProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NativeAppController extends AbstractController
{
    #[Route('/activate', name: 'activate')]
    public function activate(): Response
    {
        // TODO: REDIRECT (https://github.com/thephpleague/oauth2-github)

        $provider = new PimProvider([
            'clientId' => '5_3mduyalq0iucc8840s888gckow8k0s4wc80s0kwkkogk4ccogw',
            'clientSecret' => '40kd94hd8xicsk4g484ssoc4s4c8scsgkcgogw0w4c8oc8ogww',
            'redirectUri' => 'http://localhost:8081/callback',
        ]);

        if (!isset($_GET['code'])) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();
            header('Location: ' . $authUrl);
            exit;
        }

        // Check given state against previously stored one to mitigate CSRF attack
        if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code']
        ]);

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the user's details
            $user = $provider->getResourceOwner($token);
        } catch (\Exception $e) {

            // Failed to get user details
            exit('Oh dear...');
        }

        // Use this to interact with an API on the users behalf
        echo $token->getToken();

        return new JsonResponse(['token' => $token->getToken()]);
    }
}
