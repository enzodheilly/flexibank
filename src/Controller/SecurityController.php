<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // Récupérer le paramètre de requête "confirmation"
        $confirmation = $request->query->get('confirmation', false);
    
        // Récupérer l'erreur d'authentification si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();
    
        // Récupérer le dernier nom d'utilisateur saisi
        $lastUsername = $authenticationUtils->getLastUsername();
    
        // Initialiser le message d'erreur par défaut
        $errorMessage = '';
    
        // Vérifier si une erreur d'authentification existe
        if ($error) {
            // Récupérer l'utilisateur par l'email
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $lastUsername]);
    
            if ($user) {
                // Vérifier si le compte est verrouillé
                if ($user->getLockUntil() && new \DateTime() < $user->getLockUntil()) {
                    // Si le compte est verrouillé, empêcher la connexion
                    $errorMessage = 'Votre compte est temporairement verrouillé. Réessayez dans 3 minutes.';
                } else {
                    // Si l'utilisateur a échoué 3 fois, on le bloque pour 3 minutes
                    if ($user->getFailedAttempts() >= 3) {
                        $user->setLockUntil((new \DateTime())->modify('+3 minutes'));
                        $this->entityManager->flush();
                        $errorMessage = 'Votre compte est temporairement verrouillé. Réessayez dans 3 minutes.';
                    } else {
                        // Sinon, incrémenter le nombre de tentatives échouées
                        $user->setFailedAttempts($user->getFailedAttempts() + 1);
                        $this->entityManager->flush();
                        $errorMessage = 'Adresse mail ou mot de passe incorrect';
                    }
                }
            }
        }
    
        // Retourner la vue avec le message d'erreur personnalisé
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $errorMessage, // Passer le message d'erreur personnalisé
            'confirmation' => $confirmation,
        ]);
    }
    
    
    
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
