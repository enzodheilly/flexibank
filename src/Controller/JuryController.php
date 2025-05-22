<?php

// src/Controller/JuryController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response; // ✅ Correct

class JuryController extends AbstractController
{
    #[Route('/jury/choix', name: 'jury_choose')]
    public function choose(): Response
    {
        return $this->render('jury_switch.html.twig');
    }
    
    #[Route('/jury/switch/{type}', name: 'jury_switch')]
    public function switch(string $type, SessionInterface $session)
    {
        if (!in_array($type, ['client', 'admin'])) {
            throw $this->createNotFoundException();
        }
    
        $session->set('jury_mode', $type);
    
        return $this->redirectToRoute('app_home');
    }
    

}
