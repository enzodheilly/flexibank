<?php

namespace App\Controller;

use App\Entity\LoanRequest;
use App\Form\LoanRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoanRequestController extends AbstractController
{
    #[Route('/demande-prets', name: 'loan_request')]
    public function requestLoan(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        // Créer une nouvelle demande de prêt
        $loanRequest = new LoanRequest();
        $loanRequest->setUser($user);

        // Créer et gérer le formulaire
        $form = $this->createForm(LoanRequestType::class, $loanRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir la date de demande
            $loanRequest->setRequestDate(new \DateTime());
            $em->persist($loanRequest);
            $em->flush();

            // Rediriger vers la page de succès
            return $this->redirectToRoute('loan_request_success');
        }

        return $this->render('loan_request/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/demande-prets/success', name: 'loan_request_success')]
    public function success()
    {
        return $this->render('loan_request/success.html.twig');
    }

    #[Route('/demande-prets/suivi', name: 'loan_request_tracking')]
    public function tracking(EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $loanRequests = $em->getRepository(LoanRequest::class)->findBy(['user' => $user]);

        return $this->render('loan_request/tracking.html.twig', [
            'loanRequests' => $loanRequests,
        ]);
    }

    #[Route('/demande-prets/annuler/{id}', name: 'cancel_loan_request')]
    public function cancelLoanRequest($id, EntityManagerInterface $em)
    {
        $loanRequest = $em->getRepository(LoanRequest::class)->find($id);

        // Vérifier si la demande existe et appartient à l'utilisateur connecté
        if ($loanRequest && $loanRequest->getUser() === $this->getUser()) {
            // Permettre l'annulation de toute demande en attente
            if ($loanRequest->getStatus() === 'En Attente') {
                $loanRequest->setStatus('annulée');
                $em->flush();
            }
        }

        return $this->redirectToRoute('loan_request_tracking');
    }
}
