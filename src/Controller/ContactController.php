<?php

// src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    
     #[Route("/contact", name:"contact")]
     
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig');
    }

    
    #[Route("/contact/submit", name:"contact_submit", methods: ["POST"])]

     
    public function submit(Request $request): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');

        // Vous pouvez maintenant traiter le message (par exemple, l'envoyer par e-mail, l'enregistrer en base de données, etc.)
        
        // Exemple d'envoi d'un e-mail (en supposant que vous ayez configuré le service de messagerie)
        $this->get('mailer')->send(
            (new \Symfony\Component\Mime\Email())
                ->from('contact@votresite.com')
                ->to('votre.email@votresite.com')
                ->subject('Nouveau message de ' . $name)
                ->text('Sujet : ' . $subject . "\n\n" . 'Message : ' . $message)
        );

        // Message de succès ou redirection
        $this->addFlash('success', 'Votre message a été envoyé avec succès.');

        return $this->redirectToRoute('contact');
    }
}

