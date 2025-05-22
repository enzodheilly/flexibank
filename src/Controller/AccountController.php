<?php

// src/Controller/AccountController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CardOrder; 
use App\Entity\LoanRequest;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

            // Supprimer les demandes associées à l'utilisateur
            // Supprimer les demandes de carte
            $cardOrders = $entityManager->getRepository(CardOrder::class)->findBy(['user' => $user]);
            foreach ($cardOrders as $cardOrder) {
                $entityManager->remove($cardOrder);
            }

            // Supprimer les demandes de prêt
            $loanRequests = $entityManager->getRepository(LoanRequest::class)->findBy(['user' => $user]);
            foreach ($loanRequests as $loanRequest) {
                $entityManager->remove($loanRequest);
            }

            // Supprimer les tickets
            $tickets = $entityManager->getRepository(Ticket::class)->findBy(['user' => $user]);
            foreach ($tickets as $ticket) {
                $entityManager->remove($ticket);
            }

            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();

            // Déconnecter l'utilisateur
            $security->logout(false);

            // Rediriger vers la page d'accueil sans message flash
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
    
            // ✅ Ajouter un message flash de confirmation
            $this->addFlash('success', ' Vos informations ont bien été mises à jour.');
    
            return $this->redirectToRoute('account_profile');
        }
    
        // Afficher le formulaire d'édition
        return $this->render('account/edit.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/account/change-password', name: 'account_change_password')]
    public function changePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $security->getUser();
    
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        $errorCurrentPassword = null;
    
        if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');
    
            // ❌ Mauvais mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $errorCurrentPassword = 'Mot de passe actuel incorrect.';
            } elseif ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les nouveaux mots de passe ne correspondent pas.');
                return $this->redirectToRoute('account_change_password');
            } elseif ($currentPassword === $newPassword) {
                $this->addFlash('error', 'Le nouveau mot de passe ne peut pas être identique à l\'ancien.');
                return $this->redirectToRoute('account_change_password');
            } else {
                $passwordCriteria = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';
                if (!preg_match($passwordCriteria, $newPassword)) {
                    $this->addFlash('error', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
                    return $this->redirectToRoute('account_change_password');
                }
    
                // ✅ Hash & save
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $entityManager->persist($user);
                $entityManager->flush();
    
                $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
                return $this->redirectToRoute('account_profile');
            }
        }
    
        return $this->render('account/change_password.html.twig', [
            'error_current_password' => $errorCurrentPassword,
        ]);
    }

    #[Route('/account/verify-password', name: 'account_verify_password', methods: ['POST'])]
    public function verifyPassword(Request $request, UserPasswordHasherInterface $passwordHasher, Security $security): JsonResponse
    {
        $user = $security->getUser();
    
        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non authentifié.'], 401);
        }
    
        $currentPassword = $request->request->get('current_password');
    
        if ($passwordHasher->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse(['success' => true]);
        }
    
        return new JsonResponse(['success' => false, 'message' => 'Mot de passe actuel incorrect.']);
    }
}
