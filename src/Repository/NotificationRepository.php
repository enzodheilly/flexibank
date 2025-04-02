<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    // Récupérer les notifications non lues avec les informations du virement
    public function findUnreadNotifications(User $user): array
    {
        // Création de la requête pour récupérer les notifications non lues
        $qb = $this->createQueryBuilder('n')
            ->leftJoin('n.transfer', 't')  // On joint la table des virements
            ->leftJoin('t.fromAccount', 'f')  // On joint la table des comptes (expéditeurs)
            ->leftJoin('f.user', 's')  // On joint l'utilisateur (expéditeur)
            ->andWhere('n.user = :user')   // Filtrer par utilisateur
            ->andWhere('n.isRead = :isRead') // Notifications non lues (isRead = false)
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->orderBy('n.createdAt', 'DESC');

        // Récupérer les résultats
        $notifications = $qb->getQuery()->getResult();

        // Ajouter le message personnalisé avec le nom et prénom de l'expéditeur et la description du virement
        foreach ($notifications as $notification) {
            if ($notification->getTransfer()) {
                $transfer = $notification->getTransfer();
                $sender = $transfer->getFromAccount()->getUser(); // Récupérer l'expéditeur via le compte
                $description = 'Vous avez reçu un virement de ' 
                         . $sender->getFirstName() . ' ' 
                         . $sender->getLastName() 
                         . ' de ' 
                         . $transfer->getAmount() 
                         . '€ - ' 
                         . $transfer->getDescription();
                $notification->setDescription($description);  // Mettre à jour le message de la notification
            }
        }

        return $notifications;
    }
}
