<?php

namespace App\Controller;

use App\Repository\BankAccountRepository;
use App\Repository\TransferRepository; // Ajoutez le repository pour Transfer
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Dashboard2Controller extends AbstractController
{
    #[Route('/dashboard2', name: 'dashboard2')]
    public function index(
        BankAccountRepository $bankAccountRepository,
        TransferRepository $transferRepository // Injectez le TransferRepository ici
    ): Response
    {
        // Vérification si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération des comptes bancaires de l'utilisateur
        $currentAccount = $bankAccountRepository->findCurrentAccountByUser($user);  // Mise à jour ici
        $savingsAccount = $bankAccountRepository->findSavingsAccountByUser($user);  // Mise à jour ici

        // Récupérer les transferts récents pour l'utilisateur (si le compte courant existe)
        $recentTransfers = [];
        if ($currentAccount) {
            $recentTransfers = $transferRepository->findBy(
                ['fromAccount' => $currentAccount],  // Utilisez le compte courant (ou un autre compte si besoin)
                ['date' => 'DESC'],  // Changement de 'createdAt' en 'date'
                5  // Nombre de transferts à récupérer
            );
        }

        // Rendu de la vue avec les variables nécessaires
        return $this->render('dashboard2/index.html.twig', [
            'currentAccount' => $currentAccount,  // Mise à jour ici
            'savingsAccount' => $savingsAccount,  // Mise à jour ici
            'recentTransfers' => $recentTransfers,  // Ajouter les transferts récents ici
        ]);
    }

    #[Route('/recent-transfers', name: 'recent_transfers')]
    public function recentTransfers(
        TransferRepository $transferRepository
    ): Response
    {
        // Vérification si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération du compte courant
        $currentAccount = $this->getUser()->getAccounts();

        // Récupération des transferts récents
        $recentTransfers = $transferRepository->findBy(
            ['fromAccount' => $currentAccount], // ou 'toAccount' en fonction de ce que tu veux
            ['date' => 'DESC'],
            5 // Limité à 5 transferts récents
        );

        // Rendu du template des transferts récents
        return $this->render('recent_transfers.html.twig', [
            'recentTransfers' => $recentTransfers,
        ]);
    }
}
