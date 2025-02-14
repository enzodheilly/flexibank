<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InvestissementsController extends AbstractController
{
    
      #[Route("/investissements", name: "app_investissements")]
     
    public function investissements()
    {
        // Logique pour récupérer les investissements de l'utilisateur
        // Exemple : $investissements = $this->getUser()->getInvestissements();

        return $this->render('investissements/investissements.html.twig', [
            // 'investissements' => $investissements
        ]);
    }
}
