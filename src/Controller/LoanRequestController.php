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
        // Créer un nouvel objet LoanRequest
        $loanRequest = new LoanRequest();
        
        // Créer le formulaire simple avec LoanRequestType
        $form = $this->createForm(LoanRequestType::class, $loanRequest);
        
        // Traiter la requête et valider le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les données dans la base de données
            $em->persist($loanRequest);
            $em->flush();

            // Rediriger après soumission réussie
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
}
