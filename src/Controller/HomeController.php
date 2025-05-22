<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route("/", name: "app_home")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur est un client réel ou en mode jury client
        $session = $request->getSession();
        $juryMode = $session->get('jury_mode'); // 'client', 'admin' ou null

        $isClient = false;
        $isAdmin = false;

        if ($user) {
            $roles = $user->getRoles();

            $isClient = in_array('ROLE_CLIENT', $roles) || ($juryMode === 'client' && in_array('ROLE_JURY', $roles));
            $isAdmin = in_array('ROLE_ADMIN', $roles) || ($juryMode === 'admin' && in_array('ROLE_JURY', $roles));
            
        }

        // Formulaire newsletter
        $subscriber = new Newsletter();
        $form = $this->createForm(NewsletterFormType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subscriber->setCreatedAt(new \DateTimeImmutable());
            $em->persist($subscriber);
            $em->flush();

            $this->addFlash('success', 'Merci pour votre inscription à la newsletter !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'isClient' => $isClient,
            'isAdmin' => $isAdmin,
            'juryMode' => $juryMode, // ✅ Ajoute cette ligne
            'newsletterForm' => $form->createView(),
        ]);
        
    }
}
