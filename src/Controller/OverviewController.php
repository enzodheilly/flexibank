<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    #[Route('/dashboard/overview', name: 'app_overview')]
    public function index(): Response
    {
        // Exemple de données (à remplacer par vos données réelles)
        $accounts = [
            ['name' => 'Compte courant', 'balance' => 1234.56, 'number' => '****1234'],
            ['name' => 'Épargne', 'balance' => 5000.00, 'number' => '****5678'],
        ];

        $transactions = [
            ['date' => '20/12', 'amount' => -15.99, 'description' => 'Netflix'],
            ['date' => '19/12', 'amount' => 2000.00, 'description' => 'Salaire'],
            ['date' => '18/12', 'amount' => -75.00, 'description' => 'Carburant'],
        ];

        $budget = [
            'Alimentation' => 40,
            'Logement' => 30,
            'Loisirs' => 20,
            'Autres' => 10,
        ];

        $crypto = [
            ['name' => 'Bitcoin', 'amount' => 0.02, 'value' => 500],
            ['name' => 'Ethereum', 'amount' => 0.5, 'value' => 1000],
        ];

        return $this->render('dashboard/overview.html.twig', [
            'accounts' => $accounts,
            'transactions' => $transactions,
            'budget' => $budget,
            'crypto' => $crypto,
        ]);
    }
}
