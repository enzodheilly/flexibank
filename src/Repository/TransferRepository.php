<?php

namespace App\Repository;

use App\Entity\Transfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transfer::class);
    }

    // Méthode pour récupérer les transferts récents
    public function findRecentTransfers(int $limit = 5)
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.date', 'DESC')  // Tri par date
            ->setMaxResults($limit)  // Limite à 5 résultats
            ->getQuery()
            ->getResult();
    }

    // Nouvelle méthode pour récupérer les transferts par statut
    public function findTransfersByStatus(string $status)
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.date', 'DESC')  // Tri par date
            ->getQuery()
            ->getResult();
    }
}

