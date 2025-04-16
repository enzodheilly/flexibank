<?php

// src/Controller/TicketController.php
namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
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
    public function createTicket(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'email du formulaire
            $email = $ticket->getEmail();

            // Chercher l'utilisateur par email
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $ticket->setUser($user);
                $ticket->setSubmissionDate(new \DateTimeImmutable());

                $entityManager->persist($ticket);
                $entityManager->flush();

                $this->addFlash('success', 'Votre demande a été reçue et sera traitée par notre support.');
                return $this->redirectToRoute('ticket_create');
            } else {
                $this->addFlash('error', 'L\'utilisateur avec cet email n\'a pas été trouvé.');
            }
        }

        return $this->render('pages/ticket_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
}

