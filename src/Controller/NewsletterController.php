<?php
// src/Controller/NewsletterController.php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NewsletterController extends AbstractController
{
    
     #[Route("/newsletter", name:"newsletter")]
     
    public function subscribe(Request $request, MailerInterface $mailer)
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'email dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($newsletter);
            $entityManager->flush();

            // Envoi de l'email de confirmation
            $email = (new Email())
                ->from('noreply@tonsite.com')
                ->to($newsletter->getEmail())
                ->subject('Confirmation d\'abonnement à la Newsletter')
                ->html('<p>Merci de vous être abonné à notre newsletter !</p>');
            
            $mailer->send($email);

            // Message de succès
            $this->addFlash('success', 'Vous êtes maintenant abonné à notre newsletter !');

            return $this->redirectToRoute('home'); // Redirige vers la page d'accueil
        }

        return $this->render('newsletter/subscribe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
