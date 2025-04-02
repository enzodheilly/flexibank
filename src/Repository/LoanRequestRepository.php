<?php

namespace App\Repository;

use App\Entity\LoanRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LoanRequest>
 */
class LoanRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoanRequest::class);
    }

    /**
     * Trouve toutes les demandes de prêt en attente.
     *
     * @return LoanRequest[]
     */
    public function findPendingLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->where('LOWER(l.status) = :status')
            ->setParameter('status', 'en attente')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les demandes de prêt approuvées.
     *
     * @return LoanRequest[]
     */
    public function findApprovedLoans(): array
    {
        return $this->findBy(['status' => 'Approuvé']);
    }

    /**
     * Trouve toutes les demandes de prêt refusées.
     *
     * @return LoanRequest[]
     */
    public function findRejectedLoans(): array
    {
        return $this->findBy(['status' => 'Refusé']);
    }
    
    public function findByUserNameOrFirstName(string $search): array
    {
        return $this->createQueryBuilder('lr')
            ->innerJoin('lr.user', 'u')  // Assurez-vous que 'user' est bien la relation avec l'utilisateur
            ->where('u.nom LIKE :search OR u.prenom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
}