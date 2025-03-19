<?php

namespace App\Repository;

use App\Entity\BankAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BankAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankAccount::class);
    }

    // Méthode pour récupérer un compte courant par utilisateur
    public function findCurrentAccountByUser($user)
    {
        return $this->findOneBy([
            'user' => $user,
            'accountType' => BankAccount::ACCOUNT_TYPE_COURANT
        ]);
    }

    // Méthode pour récupérer un compte épargne par utilisateur
    public function findSavingsAccountByUser($user)
    {
        return $this->findOneBy([
            'user' => $user,
            'accountType' => BankAccount::ACCOUNT_TYPE_EPARGNE
        ]);
    }

    // Nouvelle méthode pour récupérer un compte bancaire par IBAN
    public function findByIban($iban)
    {
        return $this->findOneBy(['iban' => $iban]);
    }
}

