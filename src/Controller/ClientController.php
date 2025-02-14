<?php

// src/Controller/ClientController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\ClientStep1Type;
use App\Form\ClientStep2Type;
use App\Form\ClientStep3Type;
use App\Form\ClientStep4Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/step1', name: 'client_step1')]
    public function step1(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('app_register');  // Rediriger si l'utilisateur n'est pas trouvé
        }

        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Création du formulaire pour l'étape 1 (Pays de résidence)
        $form = $this->createForm(ClientStep1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();  // Sauvegarder les données

            return $this->redirectToRoute('client_step2');
        }

        return $this->render('client/step1.html.twig', [
            'form' => $form->createView(),
            'step' => 1,
        ]);
    }

    #[Route('/client/step2', name: 'client_step2')]
    public function step2(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('app_register');
        }

        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Création du formulaire pour l'étape 2 (Profession)
        $form = $this->createForm(ClientStep2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();  // Sauvegarder les données

            return $this->redirectToRoute('client_step3');
        }

        return $this->render('client/step2.html.twig', [
            'form' => $form->createView(),
            'step' => 2,
        ]);
    }

    #[Route('/client/step3', name: 'client_step3')]
    public function step3(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('app_register');
        }

        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Création du formulaire pour l'étape 3 (Adresse postale, code postal, ville)
        $form = $this->createForm(ClientStep3Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();  // Sauvegarder les données

            return $this->redirectToRoute('client_step4');
        }

        return $this->render('client/step3.html.twig', [
            'form' => $form->createView(),
            'step' => 3,
        ]);
    }

    #[Route('/client/step4', name: 'client_step4')]
public function step4(Request $request, EntityManagerInterface $entityManager)
{
    // Récupère l'utilisateur connecté (User existant)
    $user = $this->getUser();

    // Création du formulaire pour l'étape 4 (Numéro de téléphone)
    $form = $this->createForm(ClientStep4Type::class, $user);
    $form->handleRequest($request);

    // Si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Ajouter le rôle ROLE_CLIENT
        $user->setRoles(['ROLE_CLIENT']);  // On garde ROLE_USER et on ajoute ROLE_CLIENT

        // Sauvegarder l'utilisateur avec le nouveau rôle
        $entityManager->persist($user);
        $entityManager->flush();

        // Redirection vers la page de connexion
        return $this->redirectToRoute('app_login');
    }

    // Affichage du formulaire dans la vue twig avec l'étape
    return $this->render('client/step4.html.twig', [
        'form' => $form->createView(),
        'step' => 4,  // L'étape 4
    ]);
}

    #[Route('/client/complete', name: 'client_complete')]
    public function complete(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        // Récupérer l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');
        if (!$userId) {
            return $this->redirectToRoute('app_register');
        }

        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Marquer l'utilisateur comme client et enregistrer dans la base de données
        // Exemple : ajout d'un champ 'isClient' ou autre
        // $user->setIsClient(true); // Si tu as un champ pour l'état du client
        $entityManager->flush();  // Sauvegarder les données finales

        // Rediriger vers une page de confirmation ou de profil
        return $this->redirectToRoute('app_home'); // Par exemple
    }
}
