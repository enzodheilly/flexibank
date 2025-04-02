<?php

// src/Controller/TicketController.php
namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class TicketController extends AbstractController
{
    private $entityManager;

    // Injection de EntityManagerInterface dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tickets', name: 'ticket_list')]
    public function tickets(TicketRepository $ticketRepository): Response
    {
        // Si tu veux récupérer seulement les tickets associés à un utilisateur (client)
        $user = $this->getUser();
        $tickets = $ticketRepository->findBy(['user' => $user]);

        return $this->render('ticket/tickets.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/tickets/create', name: 'ticket_create')]
    public function createTicket(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Utiliser DateTimeImmutable
            $ticket->setSubmissionDate(new \DateTimeImmutable());  // Utilise DateTimeImmutable
            
            // Persister l'objet ticket
            $this->entityManager->persist($ticket);
            $this->entityManager->flush();
    
            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre demande a été reçue et sera traitée par notre support.');
    
            // Rediriger vers la liste des tickets
            return $this->redirectToRoute('ticket_create');
        }
    
        return $this->render('pages/ticket_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
