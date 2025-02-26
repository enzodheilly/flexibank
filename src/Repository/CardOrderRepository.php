<?php

// src/Repository/CardOrderRepository.php

namespace App\Repository;

use App\Entity\CardOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardOrder>
 */
class CardOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardOrder::class);
    }

    /**
     * Trouve toutes les commandes en attente (statut "pending" ou autre si nécessaire).
     *
     * @return CardOrder[]
     */
    public function findPendingOrders(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', 'pending') // Assure-toi que le statut correspond à celui utilisé dans ton projet
            ->getQuery()
            ->getResult();
    }
}
