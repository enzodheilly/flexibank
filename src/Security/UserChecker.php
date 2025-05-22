<?php
// src/Security/UserChecker.php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
public function checkPreAuth(UserInterface $user): void
{
    if (!$user instanceof User) {
        return;
    }

    if ($user->getLockLevel() === 3) {
        throw new CustomUserMessageAccountStatusException('Votre compte est bloqué définitivement. Contactez le support.');
    }

    if ($user->getLockUntil() && new \DateTime() < $user->getLockUntil()) {
        throw new CustomUserMessageAccountStatusException('Votre compte est temporairement verrouillé. Réessayez plus tard.');
    }
}


    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à faire ici
    }
}
