<?php

// src/Controller/Dashboard2Controller.php

namespace App\Controller;

use App\Entity\LoanRequest;
use App\Repository\BankAccountRepository;
use App\Repository\TransferRepository;
use App\Repository\NotificationRepository;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class Dashboard2Controller extends AbstractController
{
    #[Route('/dashboard2', name: 'dashboard2')]
    public function index(
        BankAccountRepository $bankAccountRepository,
        TransferRepository $transferRepository,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $em // Ajout de l'EntityManagerInterface
    ): Response
    {
        // Vérification si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération des comptes bancaires de l'utilisateur
        $currentAccount = $bankAccountRepository->findCurrentAccountByUser($user);
        $savingsAccount = $bankAccountRepository->findSavingsAccountByUser($user);

        // Récupérer les transferts récents pour l'utilisateur (si le compte courant existe)
        $recentTransfers = [];
        if ($currentAccount) {
            $recentTransfers = $transferRepository->findBy(
                ['fromAccount' => $currentAccount],
                ['date' => 'DESC'],
                5
            );
        }

        // Récupérer les notifications non lues pour l'utilisateur
        $unreadNotifications = $notificationRepository->findBy(
            ['user' => $user, 'isRead' => false], // Notifications non lues
            ['createdAt' => 'DESC']
        );

        // Vérifier s'il y a des notifications non lues
        $noNotificationsMessage = (empty($unreadNotifications)) ? "Vous n'avez aucune notification." : null;

        // Vérifier si l'utilisateur a une demande en attente
        $loanRequestInProgress = $em->getRepository(LoanRequest::class)
            ->findOneBy(['user' => $user, 'status' => 'en attente']);

        // Rendu de la vue avec les variables nécessaires
        return $this->render('dashboard2/index.html.twig', [
            'currentAccount' => $currentAccount,
            'savingsAccount' => $savingsAccount,
            'recentTransfers' => $recentTransfers,
            'unreadNotifications' => $unreadNotifications, // Passer uniquement les notifications non lues
            'noNotificationsMessage' => $noNotificationsMessage, // Passer le message d'absence de notifications
            'loanRequestInProgress' => $loanRequestInProgress, // Passer la variable de demande en attente
        ]);
    }

    #[Route('/recent-transfers', name: 'recent_transfers')]
    public function recentTransfers(
        TransferRepository $transferRepository
    ): Response
    {
        // Vérification si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération du compte courant
        $currentAccount = $this->getUser()->getAccounts();

        // Récupération des transferts récents
        $recentTransfers = $transferRepository->findBy(
            ['fromAccount' => $currentAccount],
            ['date' => 'DESC'],
            5
        );

        // Rendu du template des transferts récents
        return $this->render('recent_transfers.html.twig', [
            'recentTransfers' => $recentTransfers,
        ]);
    }

    #[Route('/notifications', name: 'notifications')]
    public function showNotifications(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté

        // Récupérer les notifications non lues de l'utilisateur
        $unreadNotifications = $notificationRepository->findBy(
            ['user' => $user, 'isRead' => false],  // Vérification des notifications non lues
            ['createdAt' => 'DESC']
        );

        // Vérifier s'il y a des notifications non lues
        if (empty($unreadNotifications)) {
            // Aucune notification non lue
            $noNotificationsMessage = "Vous n'avez aucune notification.";
        } else {
            $noNotificationsMessage = null;
        }

        // Passer les notifications au template
        return $this->render('notifications/index.html.twig', [
            'unreadNotifications' => $unreadNotifications,
            'noNotificationsMessage' => $noNotificationsMessage,  // Passer le message au template
        ]);
    }

    // Route pour marquer la notification comme lue
    #[Route('/mark-as-read/{id}', name: 'mark_as_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $em): Response
    {
        if ($notification->getUser() === $this->getUser()) {
            // Marquer la notification comme lue
            $notification->setIsRead(true);
            $em->flush(); // Sauvegarde la modification en base
        }

        // Rediriger vers la page des notifications
        return $this->redirectToRoute('dashboard2');
    }

    // Route pour créer une notification après un virement
    public function createTransferNotification($transfer, EntityManagerInterface $em)
    {
        $user = $this->getUser(); // L'utilisateur connecté

        // Récupérer l'email de l'expéditeur
        $senderEmail = $transfer->getFromAccount()->getUser()->getEmail(); // Email de l'expéditeur

        // Récupérer l'utilisateur à partir de l'email (évite d'afficher l'email)
        $sender = $em->getRepository(User::class)->findOneBy(['email' => $senderEmail]);

        if ($sender) {
            // Récupérer le nom et prénom de l'expéditeur
            $senderName = $sender->getNom(); // Nom de l'expéditeur
            $senderPrenom = $sender->getPrenom(); // Prénom de l'expéditeur

            // Créer la description de la notification sans l'email
            $description = sprintf(
                'Vous avez reçu un virement de %s %s. Description : %s',
                $senderName,  // Nom de l'expéditeur
                $senderPrenom, // Prénom de l'expéditeur
                $transfer->getDescription() // Description du virement
            );

            // Créer la notification
            $notification = new Notification();
            $notification->setUser($user) // Assigner la notification à l'utilisateur connecté
                         ->setDescription($description) // Message de la notification
                         ->setCreatedAt(new \DateTime()) // Date de création
                         ->setIsRead(false); // Par défaut, la notification est non lue

            // Sauvegarder la notification
            $em->persist($notification);
            $em->flush();
        }
    }
}
