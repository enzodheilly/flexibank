<?php
// src/Controller/CookieController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieController extends AbstractController
{
    #[Route('/cookies/accept', name: 'cookies_accept')]
    public function accept(): Response
    {
        $response = new Response();
        $cookie = Cookie::create('cookies_accepted')
            ->withValue('true')
            ->withExpires(strtotime('+1 year'))
            ->withPath('/')
            ->withSecure(true)
            ->withHttpOnly(false)
            ->withSameSite('lax');

        $response->headers->setCookie($cookie);
        $response->setContent('Cookie accepté');
        return $response;
    }

    #[Route('/cookies/refuse', name: 'cookies_refuse')]
    public function refuse(): Response
    {
        $response = new Response();
        $cookie = Cookie::create('cookies_accepted')
            ->withValue('false')
            ->withExpires(strtotime('+1 year'))
            ->withPath('/')
            ->withSecure(true)
            ->withHttpOnly(false)
            ->withSameSite('lax');

        $response->headers->setCookie($cookie);
        $response->setContent('Cookie refusé');
        return $response;
    }
}
