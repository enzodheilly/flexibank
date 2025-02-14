<?php

// src/Controller/ConditionsController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConditionsController extends AbstractController
{
    #[Route("/conditions", name: "app_conditions")]
    public function conditions(): Response
    {
        return $this->render('conditions/base.html.twig');
    }
}
