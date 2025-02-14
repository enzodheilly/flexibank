<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    #[Route('/en-savoir-plus', name: 'app_info')]
    public function index(): Response
    {
        return $this->render('info/info.html.twig');
    }
}
