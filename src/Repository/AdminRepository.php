<?php

// src/Repository/AdminRepository.php
namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

#[\Doctrine\Orm\Mapping\Entity(repositoryClass: AdminRepository::class)]
class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    // Exemple de méthode personnalisée pour récupérer un admin par email
    public function findOneByEmail(string $email): ?Admin
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Exemple pour filtrer les admins par rôle (super admin, admin, support)
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.role = :role')  // Comparaison directe avec le rôle
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }
}
