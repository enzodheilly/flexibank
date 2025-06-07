<?php

// src/Controller/Dashboard2Controller.php

namespace App\Controller;

use App\Entity\LoanRequest;
use App\Repository\BankAccountRepository;
use App\Repository\TransferRepository;
use App\Repository\NotificationRepository;
use App\Entity\Notification;
use App\Entity\Beneficiary;
use App\Form\BeneficiaryType;
use App\Repository\BeneficiaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class Dashboard2Controller extends AbstractController
{
   #[Route('/dashboard2', name: 'dashboard2')]
public function index(
    BankAccountRepository $bankAccountRepository,
    TransferRepository $transferRepository,
    NotificationRepository $notificationRepository,
    EntityManagerInterface $em,
    BeneficiaryRepository $beneficiaryRepository,
    Request $request
): Response {
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Comptes bancaires
    $currentAccount = $bankAccountRepository->findCurrentAccountByUser($user);
    $savingsAccount = $bankAccountRepository->findSavingsAccountByUser($user);

    // Transferts récents
    $recentTransfers = $currentAccount ? $transferRepository->findBy(
        ['fromAccount' => $currentAccount],
        ['date' => 'DESC'],
        5
    ) : [];

    // Notifications non lues
    $unreadNotifications = $notificationRepository->findBy(
        ['user' => $user, 'isRead' => false],
        ['createdAt' => 'DESC']
    );
    $noNotificationsMessage = empty($unreadNotifications) ? "Vous n'avez aucune notification." : null;

    // Demande de prêt en cours
    $loanRequestInProgress = $em->getRepository(LoanRequest::class)
        ->findOneBy(['user' => $user, 'status' => 'en attente']);

    // Formulaire + liste des bénéficiaires
    $beneficiary = new Beneficiary();
    $form = $this->createForm(BeneficiaryType::class, $beneficiary);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $beneficiary->setOwner($user);
        $em->persist($beneficiary);
        $em->flush();
        $this->addFlash('success', 'Bénéficiaire ajouté avec succès.');
        return $this->redirectToRoute('dashboard2');
    }

    $beneficiaries = $beneficiaryRepository->findBy(['owner' => $user]);

    return $this->render('dashboard2/index.html.twig', [
        'currentAccount' => $currentAccount,
        'savingsAccount' => $savingsAccount,
        'recentTransfers' => $recentTransfers,
        'unreadNotifications' => $unreadNotifications,
        'noNotificationsMessage' => $noNotificationsMessage,
        'loanRequestInProgress' => $loanRequestInProgress,
        'form' => $form->createView(),
        'beneficiaries' => $beneficiaries,
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

    #[Route('/dashboard2/beneficiaries', name: 'beneficiary_list')]
public function manageBeneficiaries(
    Request $request,
    BeneficiaryRepository $beneficiaryRepository,
    EntityManagerInterface $em
): Response {
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    // Formulaire d'ajout de bénéficiaire
    $beneficiary = new Beneficiary();
    $form = $this->createForm(BeneficiaryType::class, $beneficiary);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $beneficiary->setOwner($user);
        $em->persist($beneficiary);
        $em->flush();
        $this->addFlash('success', 'Bénéficiaire ajouté avec succès.');
        return $this->redirectToRoute('beneficiary_list');
    }

    // Liste des bénéficiaires
    $beneficiaries = $beneficiaryRepository->findBy(['owner' => $user]);

    return $this->render('dashboard2/beneficiaries.html.twig', [
        'beneficiaries' => $beneficiaries,
        'form' => $form->createView(),
    ]);
}

#[Route('/dashboard2/beneficiaries/delete/{id}', name: 'beneficiary_delete', methods: ['POST'])]
public function deleteBeneficiary(
    Request $request,
    Beneficiary $beneficiary,
    EntityManagerInterface $em,
    CsrfTokenManagerInterface $csrfTokenManager
): Response {
    $user = $this->getUser();
    if ($beneficiary->getOwner() !== $user) {
        throw $this->createAccessDeniedException();
    }

    $token = $request->request->get('_token');
    if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $beneficiary->getId(), $token))) {
        throw $this->createAccessDeniedException('Token CSRF invalide.');
    }

    $em->remove($beneficiary);
    $em->flush();

    $this->addFlash('success', 'Bénéficiaire supprimé.');
    return $this->redirectToRoute('beneficiary_list');
}

#[Route('/dashboard2/beneficiaries/edit/{id}/modal', name: 'beneficiary_edit_modal', methods: ['GET'])]
public function editModal(
    Beneficiary $beneficiary,
    Request $request,
    EntityManagerInterface $em
): Response {
    $user = $this->getUser();
    if ($beneficiary->getOwner() !== $user) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(BeneficiaryType::class, $beneficiary);

    return $this->render('dashboard2/_edit_modal.html.twig', [
        'form' => $form->createView(),
        'beneficiary' => $beneficiary,
    ]);
}

#[Route('/dashboard2/beneficiaries/edit/{id}', name: 'beneficiary_edit', methods: ['POST'])]
public function editSubmit(
    Beneficiary $beneficiary,
    Request $request,
    EntityManagerInterface $em
): Response {
    $user = $this->getUser();
    if ($beneficiary->getOwner() !== $user) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(BeneficiaryType::class, $beneficiary);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Bénéficiaire mis à jour avec succès.');
    }

    return $this->redirectToRoute('beneficiary_list');
}


}
