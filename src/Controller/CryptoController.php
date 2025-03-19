<?php

// src/Controller/CryptoController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CryptoController extends AbstractController
{
    #[Route("/crypto", name:"crypto")]
    public function index(): Response
    {
        return $this->render('crypto_tracker/index.html.twig');
    }

    #[Route("/crypto/buy", name:"crypto_buy")]
    public function buy(): Response
    {
        // Logique pour l'achat de crypto-monnaie
        return $this->render('crypto/buy.html.twig');
    }

    #[Route('/crypto/market', name: 'crypto_market')]
    public function market(): Response
    {
        return $this->render('crypto/market.html.twig');
    }

    // Ajoute la route 'transfer_create' ici (si c'est bien ce que tu veux)
    #[Route('/crypto/transfer/create', name: 'transfer_create')]
    public function transferCreate(): Response
    {
        // Logique pour la création du transfert
        return $this->render('crypto/transfer_create.html.twig');
    }
}
