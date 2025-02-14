<?php

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/", name: "app_home")]
    public function index(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Vérifie si l'utilisateur est un client (nous utilisons la méthode isClient ajoutée à l'entité User)
        $isClient = $user ? $user->isClient() : false;

        // Passe la variable isClient à la vue Twig
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'isClient' => $isClient, // Passe la variable isClient à la vue
        ]);
    }
}
