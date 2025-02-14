<?php

// src/Controller/ProfileController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Transaction;

class ProfileController extends AbstractController
{
    #[Route("/profile", name: "app_profile")]
    public function profile(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les transactions de l'utilisateur
        $transactions = $entityManager->getRepository(Transaction::class)->findBy([
            'user' => $user
        ]);

        // Retourner la page de profil avec les transactions
        return $this->render('profile/index.html.twig', [
            'transactions' => $transactions, // On passe les transactions à Twig
        ]);
    }
}
