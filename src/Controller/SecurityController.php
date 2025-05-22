<?php

// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Security;


class SecurityController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

#[Route(path: '/login', name: 'app_login')]
public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
{
    $confirmation = $request->query->get('confirmation', false);

    // Réinitialiser le last_username si une confirmation vient de l'inscription
    if ($confirmation) {
        $request->getSession()->remove('_security.last_username');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();
    $errorMessage = '';

    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $lastUsername]);

    if ($user) {
        $now = new \DateTime();

        if ($user->getLockUntil() && $now < $user->getLockUntil()) {
            $errorMessage = match($user->getLockLevel()) {
                1 => 'Compte verrouillé 3 min.',
                2 => 'Compte verrouillé 15 min.',
                3 => 'Compte bloqué définitivement. Contactez le support.',
                default => 'Votre compte est temporairement verrouillé.'
            };
        } elseif ($error) {
            $failed = $user->getFailedAttempts() + 1;
            $user->setFailedAttempts($failed);

            if ($failed === 3) {
                $user->setLockLevel(1);
                $user->setLockUntil((new \DateTime())->modify('+3 minutes'));
                $errorMessage = 'Trop de tentatives. Compte verrouillé 3 minutes.';
            } elseif ($failed === 4) {
                $user->setLockLevel(2);
                $user->setLockUntil((new \DateTime())->modify('+15 minutes'));
                $errorMessage = 'Nouvelles tentatives échouées. Compte verrouillé 15 minutes.';
            } elseif ($failed >= 5) {
                $user->setLockLevel(3);
                $user->setLockUntil(null);
                $errorMessage = 'Compte bloqué définitivement. Contactez le support.';
            } else {
                $errorMessage = 'Adresse mail ou mot de passe incorrect';
            }

            $this->entityManager->flush();
        } elseif (!$error) {
            $user->setFailedAttempts(0);
            $user->setLockUntil(null);
            $user->setLockLevel(0);
            $this->entityManager->flush();
        }
    } elseif ($error) {
        $errorMessage = 'Adresse mail ou mot de passe incorrect';
    }

    return $this->render('security/login.html.twig', [
        'last_username' => $confirmation ? '' : $lastUsername,
        'error' => $errorMessage,
        'confirmation' => $confirmation,
    ]);
}


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode peut rester vide - elle est interceptée par Symfony.');
    }
}
