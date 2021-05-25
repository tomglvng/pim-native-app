<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NativeAppController extends AbstractController
{
    #[Route('/activate', name: 'activate')]
    public function activate(): Response
    {
        // TODO: REDIRECT (https://github.com/thephpleague/oauth2-github)

        return $this->render('native_app/index.html.twig', [
            'controller_name' => 'NativeAppController',
        ]);
    }
}
