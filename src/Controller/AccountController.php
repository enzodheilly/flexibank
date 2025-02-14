<?php

// src/Controller/AccountController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AccountController extends AbstractController
{
    #[Route("/account/delete", name: "app_account_delete")]
public function delete(
    Request $request, 
    EntityManagerInterface $entityManager, 
    Security $security
): Response {
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Vérifier le jeton CSRF
    if ($request->isMethod('POST')) {
        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_csrf_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        // Déconnecter l'utilisateur
        $security->logout(false);

        // Supprimer l'utilisateur
        $entityManager->remove($user);
        $entityManager->flush();

        // Rediriger vers la page d'accueil avec un message flash
        $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        return $this->redirectToRoute('app_home');
    }

    // Afficher la page de confirmation de suppression
    return $this->render('account/delete.html.twig');
}


    #[Route('/mon-compte', name: 'account_profile')]
    public function index(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Passe l'utilisateur à la vue
        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/upload-photo', name: 'account_upload_photo', methods: ['POST'])]
    public function uploadProfilePicture(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $file = $request->files->get('profile_picture');

        if ($file) {
            // Créer un nom de fichier unique
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                // Déplace le fichier dans le répertoire de stockage
                $file->move(
                    $this->getParameter('profile_pictures_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // En cas d'erreur, rediriger avec un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de l\'upload de la photo');
                return $this->redirectToRoute('account_profile');
            }

            // Mettre à jour l'utilisateur avec la nouvelle photo de profil
            $user->setProfilePicture($newFilename);

            // Sauvegarder les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Photo de profil mise à jour avec succès');

            return $this->redirectToRoute('account_profile');
        }

        // Si aucune photo n'est envoyée
        $this->addFlash('error', 'Aucune photo sélectionnée');
        return $this->redirectToRoute('account_profile');
    }

    #[Route('/account/edit', name: 'account_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Traitement des informations à modifier
        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            $user->setProfession($request->request->get('profession'));
            $user->setAdressePostale($request->request->get('adressePostale'));
            $user->setCodePostal($request->request->get('codePostal'));
            $user->setVille($request->request->get('ville'));
            $user->setNumeroTelephone($request->request->get('numeroTelephone'));

            // Sauvegarder les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Informations mises à jour avec succès');

            return $this->redirectToRoute('account_profile');
        }

        // Afficher le formulaire d'édition
        return $this->render('account/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/account/change-password', name: 'account_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, Security $security, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            // Vérifier si le mot de passe actuel est correct
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect');
                return $this->redirectToRoute('account_change_password');
            }

            // Vérifier si les nouveaux mots de passe correspondent
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                return $this->redirectToRoute('account_change_password');
            }

            // Hasher le nouveau mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

            // Sauvegarder les modifications
            $entityManager->persist($user);
            $entityManager->flush();

            // Afficher un message de succès
            $this->addFlash('success', 'Mot de passe changé avec succès');

            return $this->redirectToRoute('account_profile');
        }

        // Afficher le formulaire de changement de mot de passe
        return $this->render('account/change_password.html.twig');
    }
}
