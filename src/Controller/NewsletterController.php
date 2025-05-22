<?php
// src/Controller/NewsletterController.php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class NewsletterController extends AbstractController
{
    
    #[Route('/newsletter', name: 'newsletter', methods: ['POST'])]
    public function newsletter(Request $request, EntityManagerInterface $em): Response
    {
        $subscriber = new Newsletter();
        $form = $this->createForm(NewsletterFormType::class, $subscriber);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $subscriber->getEmail();
    
            // Vérifie si l'email existe déjà dans la base
            $existingSubscriber = $em->getRepository(Newsletter::class)->findOneBy(['email' => $email]);
    
            if ($existingSubscriber) {
                $this->addFlash('success', '⚠️ Cette adresse e-mail est déjà inscrite.');
                return $this->redirect($this->generateUrl('app_home') . '#newsletter');
            }
    
            $subscriber->setCreatedAt(new \DateTimeImmutable());
            $em->persist($subscriber);
            $em->flush();
    
            $this->addFlash('success', '✅ Merci pour votre inscription à la newsletter !');
            return $this->redirect($this->generateUrl('app_home') . '#newsletter');
        }
    
        return $this->redirectToRoute('app_home');
    }
    

    #[Route('/newsletter/export', name: 'newsletter_export')]
    public function export(Request $request, EntityManagerInterface $em): Response
    {
        // Récupère le mode jury depuis la session
        $juryMode = $request->getSession()->get('jury_mode');
    
        // Vérifie que l'utilisateur est admin ou jury en mode admin
        if (!$this->isGranted('ROLE_ADMIN') && $juryMode !== 'admin') {
            throw $this->createAccessDeniedException('Accès réservé aux administrateurs.');
        }
    
        // Récupération des inscrits à la newsletter
        $subscribers = $em->getRepository(Newsletter::class)->findAll();
    
        // Préparation des données CSV
        $csvHeader = ['email', 'date_inscription'];
        $rows = [];
    
        foreach ($subscribers as $subscriber) {
            $rows[] = [
                $subscriber->getEmail(),
                $subscriber->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }
    
        // Création du fichier CSV en mémoire
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
    
        // Retourne le fichier CSV en réponse téléchargeable
        return new Response(
            $content,
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="newsletter_export.csv"',
            ]
        );
    }
    

}
