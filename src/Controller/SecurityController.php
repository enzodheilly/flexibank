<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer le paramètre de requête "confirmation"
        $confirmation = $request->query->get('confirmation', false);

        // Récupérer l'erreur d'authentification si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        // Vérifier si l'utilisateur est authentifié (connecté)
        if ($this->getUser()) {
            $user = $this->getUser();

            // Si l'utilisateur est bloqué, empêcher la connexion
            if ($user->getStatus() === 'Bloqué') {
                // Lancer une exception pour empêcher la connexion
                throw new CustomUserMessageAuthenticationException('Votre compte est bloqué. Veuillez contacter l\'administrateur.');
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'confirmation' => $confirmation,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
