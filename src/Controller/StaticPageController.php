<?php

// src/Controller/StaticPageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaticPageController extends AbstractController
{
    #[Route(path: '/conditionsgénérales', name: 'conditionsgénérales')]
    public function conditionsgénérales(): Response
    {
        return $this->render('pages/conditionsgénérales.html.twig');
    }

    #[Route(path: '/politique', name: 'politique')]

    public function politique(): Response
    {
        return $this->render('pages/politique.html.twig');
    }

    #[Route(path: '/mentions', name: 'mentions')]

    public function mentions(): Response
    {
        return $this->render('pages/mentions.html.twig');
    }

    #[Route(path: '/cookle', name: 'cookie')]

    public function cookie(): Response
    {
        return $this->render('pages/cookie.html.twig');
    }
    
    #[Route(path: '/propos', name: 'propos')]
    public function propos(): Response
    {
        return $this->render('pages/propos.html.twig');
    }

    #[Route(path: '/nosservices', name: 'nosservices')]
    public function nosservices(): Response
    {
        return $this->render('pages/nosservices.html.twig');
    }

    #[Route(path: '/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('pages/faq.html.twig');
    }

    #[Route(path: '/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }
}
