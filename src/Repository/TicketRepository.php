<?php

// src/Repository/TicketRepository.php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function countTicketsByStatus(string $status): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function countTicketsByPriority(string $priority): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.priority = :priority')
            ->setParameter('priority', $priority)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTicketsByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.submissionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOpenTicket(int $id): ?Ticket
{
    return $this->createQueryBuilder('t')
        ->where('t.id = :id')
        ->andWhere('t.status != :status')
        ->setParameter('id', $id)
        ->setParameter('status', 'Fermé')
        ->getQuery()
        ->getOneOrNullResult();
}

}