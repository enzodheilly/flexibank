<?php

namespace App\Controller;

use App\Entity\CardOrder;
use App\Form\CardOrderType;
use App\Service\CardGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardOrderController extends AbstractController
{
    private $cardGenerator;

    // Injection du service CardGeneratorService
    public function __construct(CardGeneratorService $cardGenerator)
    {
        $this->cardGenerator = $cardGenerator;
    }

    #[Route('/dashboard', name: 'card_dashboard', methods: ['GET', 'POST'])]
    public function dashboard(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Redirection si l'utilisateur n'est pas connecté
        }

        // Récupérer la commande de carte associée à l'utilisateur
        $cardOrder = $entityManager->getRepository(CardOrder::class)
                                   ->findOneBy(['userEmail' => $user->getEmail()]);

        // Créer une nouvelle commande de carte
        $newCardOrder = new CardOrder();
        $newCardOrder->setUserEmail($user->getEmail());

        // Générer les informations de la carte via le service
        $newCardOrder->setCardNumber($this->cardGenerator->generateCardNumber());
        $newCardOrder->setCcv($this->cardGenerator->generateCCV());
        $newCardOrder->setExpirationDate($this->cardGenerator->generateExpirationDate());

        // Créer et traiter le formulaire
        $form = $this->createForm(CardOrderType::class, $newCardOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newCardOrder->setStatus('pending');
            $newCardOrder->setOrderDate(new \DateTime());

            // Enregistrer la commande dans la base de données
            $entityManager->persist($newCardOrder);
            $entityManager->flush();

            $this->addFlash('success', 'Votre nouvelle commande de carte a été passée avec succès !');
            return $this->redirectToRoute('track_card_order');
        }

        return $this->render('card_order/dashboard.html.twig', [
            'user' => $user,         // Passe l'utilisateur au template
            'cardOrder' => $cardOrder, // Passe la commande de carte
            'form' => $form->createView(), // Passe le formulaire au template
        ]);
    }

    #[Route('/order-card', name: 'order_card', methods: ['GET', 'POST'])]
    public function orderCard(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('order_card');
        }

        $userEmail = $user->getEmail();
        if (!$userEmail) {
            $this->addFlash('error', 'Aucun email trouvé pour cet utilisateur.');
            return $this->redirectToRoute('order_card');
        }

        // Créer une nouvelle commande de carte
        $cardOrder = new CardOrder();
        $cardOrder->setUserEmail($userEmail);

        // Générer les informations de la carte via le service
        $cardOrder->setCardNumber($this->cardGenerator->generateCardNumber());
        $cardOrder->setCcv($this->cardGenerator->generateCCV());
        $cardOrder->setExpirationDate($this->cardGenerator->generateExpirationDate());

        // Créer et traiter le formulaire
        $form = $this->createForm(CardOrderType::class, $cardOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cardOrder->setStatus('pending');
            $cardOrder->setOrderDate(new \DateTime());

            // Enregistrer la commande dans la base de données
            $entityManager->persist($cardOrder);
            $entityManager->flush();

            $this->addFlash('success', 'Votre nouvelle commande de carte a été passée avec succès !');
            return $this->redirectToRoute('track_card_order');
        }

        return $this->render('card_order/order.html.twig', [
            'form' => $form->createView(), // Passe le formulaire à la vue
        ]);
    }

    #[Route('/track-card-order', name: 'track_card_order', methods: ['GET'])]
    public function trackCardOrder(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('track_card_order');
        }

        $userEmail = $user->getEmail();
        $cardOrder = $entityManager->getRepository(CardOrder::class)->findOneBy([
            'userEmail' => $userEmail,
            'status' => 'pending'
        ]);

        if (!$cardOrder) {
            return $this->render('card_order/track.html.twig', [
                'message' => 'Aucune commande en attente.'
            ]);
        }

        return $this->render('card_order/dashboard.html.twig', [
            'cardOrder' => $cardOrder
        ]);
    }

    #[Route('/view-card', name: 'app_view_card')]
    public function viewCard(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $cardOrder = $entityManager->getRepository(CardOrder::class)
                           ->findOneBy(['userEmail' => $user->getEmail()]);

        if (!$cardOrder) {
            throw $this->createNotFoundException('Carte non trouvée pour cet utilisateur');
        }

        return $this->render('card_order/view.html.twig', [
            'cardOrder' => $cardOrder
        ]);
    }
}
