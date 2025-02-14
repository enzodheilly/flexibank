<?php

// src/Controller/TransferController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/transfer', name: 'app_transfer', methods: ['POST'])]
    public function transfer(Request $request): Response
    {
        $sourceAccount = $request->request->get('sourceAccount');
        $destinationAccount = $request->request->get('destinationAccount');
        $amount = $request->request->get('amount');

        // Validation et logique de transfert
        if ($amount <= 0) {
            $this->addFlash('error', 'Le montant doit être supérieur à zéro.');
            return $this->redirectToRoute('app_profil');
        }

        // Exemple de traitement
        // Logique pour débiter le compte source et créditer le compte destinataire

        $this->addFlash('success', 'Virement effectué avec succès !');
        return $this->redirectToRoute('app_profil');
    }
}
