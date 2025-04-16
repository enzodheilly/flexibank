<?php

// src/Controller/DashboardController.php
namespace App\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\CardOrderType;
use App\Entity\CardOrder;
use App\Service\CardGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'card_dashboard')]
    public function index(Request $request, EntityManagerInterface $em, UserInterface $user, CardGeneratorService $cardGenerator): Response
    {
        // Créez une nouvelle commande
        $cardOrder = new CardOrder();
        $form = $this->createForm(CardOrderType::class, $cardOrder);

        // Traitez le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générez les informations de la carte via le CardGeneratorService
            $cardOrder->setCardNumber($cardGenerator->generateCardNumber()); // Générer le numéro de carte
            $cardOrder->setCcv($cardGenerator->generateCCV()); // Générer le CCV
            $cardOrder->setExpirationDate($cardGenerator->generateExpirationDate()); // Générer la date d'expiration

            // Associez la commande à l'utilisateur connecté
            $cardOrder->setUser($user);  // On suppose que CardOrder a un champ 'user'
            $cardOrder->setStatus('En Attente'); 

            $em->persist($cardOrder);
            $em->flush();

            // Redirige vers le dashboard après soumission
            return $this->redirectToRoute('card_dashboard');
        }

        // Récupérez les commandes existantes de l'utilisateur connecté
        $orders = $em->getRepository(CardOrder::class)->findBy(['user' => $user]);

        // Logique pour définir la variable show_order_tracking
        $show_order_tracking = !empty($orders);  // Exemple simple : affiche le suivi si l'utilisateur a des commandes

        return $this->render('dashboard/dashboard.html.twig', [
            'form' => $form->createView(),
            'orders' => $orders,  // Passez les commandes de l'utilisateur connecté
            'show_order_tracking' => $show_order_tracking,  // Passe la variable au template
        ]);
    }

    #[Route('/virtual-card/submit', name: 'virtual_card_submit', methods: ['POST'])]
    public function submit(Request $request): Response
    {
        // Traite la demande de carte ici (simulé)
        
        // Redirige vers la page de dashboard où le suivi de commande est inclus
        return $this->redirectToRoute('card_dashboard');  // Remplacez par le nom de la route vers le dashboard
    }

   #[Route('/virtual-card', name: 'virtual_card')]
public function virtualCard(EntityManagerInterface $em, UserInterface $user): Response
{
    // Récupérer la carte virtuelle de l'utilisateur (s'il en a une)
    $cardOrder = $em->getRepository(CardOrder::class)->findOneBy(['user' => $user]);

    // Si l'utilisateur n'a pas de carte, afficher un message et rediriger
    if (!$cardOrder) {
        $this->addFlash('error', "Vous n'avez pas de carte.");
        return $this->redirectToRoute('dashboard2'); // Remplace 'dashboard' par la route de ton choix
    }

    // Sinon, afficher la page carte
    return $this->render('dashboard/virtual_card.html.twig', [
        'cardOrder' => $cardOrder,
    ]);
}


}
