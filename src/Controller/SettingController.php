<?php

// src/Controller/SettingController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingController extends AbstractController
{
    #[Route("/setting", name: "app_setting")]
    public function setting(): Response
    {
        // Retourner la page de profil de l'utilisateur
        return $this->render('setting/index.html.twig');
    }
}