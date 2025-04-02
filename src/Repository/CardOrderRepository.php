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
     * Trouve toutes les commandes en attente (statut "En Attente").
     *
     * @return CardOrder[] 
     */
    public function findPendingOrders(): array
    {
        return $this->createQueryBuilder('c')
            ->where('LOWER(c.status) = :status') // Comparaison insensible à la casse
            ->setParameter('status', 'En Attente') // Assure-toi que c'est en minuscule
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les commandes approuvées (statut "Approuvé").
     *
     * @return CardOrder[]
     */
    public function findApprovedOrders(): array
    {
        return $this->findBy(['status' => 'Approuvé']);
    }

    /**
     * Trouve toutes les commandes refusées (statut "Refusé").
     *
     * @return CardOrder[]
     */
    public function findRejectedOrders(): array
    {
        return $this->findBy(['status' => 'Refusé']);
    }
}
