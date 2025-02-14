<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VirementController extends AbstractController
{
    #[Route('/virement/effectuer', name: 'virement_effectuer', methods: ['POST'])]
    public function effectuerVirement(Request $request, EntityManagerInterface $em): Response
    {
        $montant = $request->request->get('montant');
        $beneficiaireId = $request->request->get('beneficiaire');

        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $beneficiaire = $em->getRepository(User::class)->find($beneficiaireId);

        if (!$beneficiaire) {
            $this->addFlash('error', 'Bénéficiaire non trouvé.');
            return $this->redirectToRoute('app_profile');
        }

        if ($montant <= 0) {
            $this->addFlash('error', 'Montant invalide.');
            return $this->redirectToRoute('app_profile');
        }

        // Créer la transaction
        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setBeneficiaire($beneficiaire);
        $transaction->setMontant($montant);
        $transaction->setDate(new \DateTime());

        $em->persist($transaction);
        $em->flush();

        $this->addFlash('success', 'Virement effectué avec succès.');
        return $this->redirectToRoute('app_profile');
    }
}
