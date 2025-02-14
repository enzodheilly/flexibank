<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RegistrationController extends AbstractController
{
    #[Route("/register", name: "app_register")]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Créez un nouvel utilisateur
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérifiez si l'email existe déjà dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($existingUser) {
                // Ajouter un message flash d'erreur
                $this->addFlash('error', 'Un compte existe déjà avec cette adresse e-mail.');
                // Renvoyer à la même page pour afficher le message d'erreur
                return $this->redirectToRoute('app_register');
            }

            // Récupérer le mot de passe en texte brut
            $plainPassword = $form->get('plainPassword')->getData();

            // Vérifiez que le mot de passe n'est pas null
            if ($plainPassword) {
                // Hash du mot de passe
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Persister l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Stocker l'ID de l'utilisateur dans la session
            $session->set('user_id', $user->getId());

            // Rediriger vers la page de conversion en client
            return $this->redirectToRoute('app_login', ['confirmation' => true]);

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
