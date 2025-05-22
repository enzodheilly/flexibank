<?php


namespace App\Controller;

use App\Entity\Admin;
use App\Entity\CardOrder;
use App\Form\AdminType;
use App\Entity\Transfer;
use App\Entity\Ticket;
use App\Entity\LoanRequest;
use App\Repository\CardOrderRepository;
use App\Repository\AdminRepository;
use App\Repository\LoanRequestRepository;
use App\Repository\TransferRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    // Route pour créer un administrateur

    #[Route('/login/admin', name: 'admin_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $UserPasswordHasher): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashing the password
            $password = $passwordEncoder->encodePassword($admin, $form->get('password')->getData());
            $admin->setPassword($password);

            // Persist the new admin
            $entityManager->persist($admin);
            $entityManager->flush();

            // Redirect to a success page or dashboard
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/create_admin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

#[Route('/admin', name: 'admin_dashboard')]
public function dashboard(UserRepository $userRepository, TransferRepository $transferRepository): Response
{
    // Vérifie que l'utilisateur a bien le rôle admin
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $user = $this->getUser();

    // Sécurité : si le compte est temporairement verrouillé
    if (method_exists($user, 'getLockUntil') && $user->getLockUntil() instanceof \DateTimeInterface) {
        if ($user->getLockUntil() > new \DateTime()) {
            $this->addFlash('danger', 'Votre compte est temporairement verrouillé.');
            return $this->redirectToRoute('app_login');
        }
    }

    // Récupération des données
    $usersCount = $userRepository->count([]);
    $activeUsersCount = $userRepository->count(['status' => 'Actif']); // Remplace par le bon champ si nécessaire
    $transfersCount = $transferRepository->count([]);

    $chartData = [
        ['date' => "Jan 23", 'transactions' => 167, 'nouveaux_utilisateurs' => 45],
        ['date' => "Fév 23", 'transactions' => 125, 'nouveaux_utilisateurs' => 56],
    ];

    $stats = [
        ['label' => 'Utilisateurs Actifs', 'value' => $activeUsersCount, 'icon' => 'users'],
        ['label' => 'Virements', 'value' => $transfersCount, 'icon' => 'receipt'],
    ];

    return $this->render('admin/dashboard.html.twig', [
        'chartData' => $chartData,
        'stats' => $stats,
    ]);
}
    // Route pour afficher les tickets

    #[Route('/admin/tickets', name: 'admin_tickets')]
public function tickets(TicketRepository $ticketRepository): Response
{
    // Comptabiliser les tickets selon leur statut et priorité
    $pendingTickets = $ticketRepository->countTicketsByStatus('En Attente');
    $resolvedTickets = $ticketRepository->countTicketsByStatus('Résolu');
    $urgentTickets = $ticketRepository->countTicketsByPriority('Urgent');  // Vérifie que 'Urgent' existe dans la base
    $basseTickets = $ticketRepository->countTicketsByPriority('Basse');    // Vérifie que 'Basse' existe dans la base

    // Récupérer tous les tickets
    $tickets = $ticketRepository->findAll();
    
    return $this->render('admin/tickets.html.twig', [
        'pendingTickets' => $pendingTickets,
        'resolvedTickets' => $resolvedTickets,
        'urgentTickets' => $urgentTickets,
        'tickets' => $tickets,
    ]);
}

#[Route('/admin/ticket/resolve/{id}', name: 'admin_ticket_resolve')]
public function resolveTicket(int $id, TicketRepository $ticketRepository, EntityManagerInterface $entityManager): Response
{
    $ticket = $ticketRepository->findOpenTicket($id);

    if (!$ticket) {
        $this->addFlash('danger', 'Ticket introuvable.');
        return $this->redirectToRoute('admin_tickets');
    }

    if ($ticket->getStatus() === 'Fermé') {
        $this->addFlash('warning', 'Ce ticket est déjà fermé et ne peut plus être modifié.');
        return $this->redirectToRoute('admin_tickets');
    }

    if ($ticket->getStatus() !== 'Résolu') {
        // Mettre à jour le statut en "Résolu"
        $ticket->setStatus('Résolu');
        
        // Si la priorité est 'Urgent', changer la priorité à "Moyenne" (ou autre si tu veux)
        if ($ticket->getPriority() === 'Urgent') {
            $ticket->setPriority('Moyenne'); // Il ne sera plus comptabilisé comme Urgent
        }

        $entityManager->flush();
        $this->addFlash('success', 'Le ticket a été marqué comme résolu.');
    } else {
        $this->addFlash('warning', 'Ce ticket est déjà résolu.');
    }

    return $this->redirectToRoute('admin_tickets');
}


    // Route pour afficher la liste des utilisateurs
    #[Route('/admin/users', name: 'admin_users')]
    public function users(UserRepository $userRepository, Request $request): Response
    {
        // Recherche par email
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $users = $userRepository->findByEmail($searchTerm);
        } else {
            $users = $userRepository->findAll();
        }

        $userData = [];
        foreach ($users as $user) {
            foreach ($user->getAccounts() as $bankAccount) {
                $userData[] = [
                    'id' => $user->getId(),
                    'name' => $user->getNom() . ' ' . $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'status' => $user->getStatus(),
                    'balance' => $bankAccount->getBalance(),
                    'lastLogin' => $user->getCreatedAt()->format('Y-m-d'),
                ];
            }
        }

        // Liste des utilisateurs bloqués
        $blockedUsers = $userRepository->findBy(['status' => 'Bloqué']);

        return $this->render('admin/users.html.twig', [
            'users' => $userData,
            'blockedUsers' => $blockedUsers,
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'admin_user_edit')]
    public function editUser(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur à modifier
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        // Si le formulaire est soumis
        if ($request->isMethod('POST')) {
            $user->setNom($request->get('nom'));
            $user->setPrenom($request->get('prenom'));
            $user->setEmail($request->get('email'));
            $user->setNumeroTelephone($request->get('numeroTelephone'));
            $user->setAdressePostale($request->get('adressePostale'));
            $user->setCodePostal($request->get('codePostal'));
            $user->setVille($request->get('ville'));
            $user->setPaysDeResidence($request->get('paysDeResidence'));
            $user->setProfession($request->get('profession'));
    
            // Gestion du mot de passe
            $password = $request->get('password');
            if ($password) {
                $user->setPassword($password); // Tu devras ajouter une gestion de hachage ici si nécessaire
            }
    
            // Sauvegarde des modifications
            $entityManager->flush();
    
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
    
            return $this->redirectToRoute('admin_users');
        }
    
        return $this->render('admin/edit_user.html.twig', [
            'user' => $user,
        ]);
    }
    

    #[Route('/admin/user/block/{id}', name: 'admin_user_block', methods: ['POST'])]
    public function blockUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        // Bloquer l'utilisateur
        $user->setStatus('Bloqué');
        
        // Enregistrer les modifications en base de données
        $entityManager->flush();
    
        $this->addFlash('success', 'Utilisateur bloqué avec succès.');
    
        return $this->redirectToRoute('admin_users');
    }
    
    #[Route('/admin/user/unblock/{id}', name: 'admin_user_unblock', methods: ['POST'])]
public function unblockUser(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    $user = $userRepository->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Débloquer l'utilisateur
    $user->setStatus('Actif');
    
    // Sauvegarder les modifications en base de données
    $entityManager->flush();

    $this->addFlash('success', 'Utilisateur débloqué avec succès.');

    return $this->redirectToRoute('admin_users');
}


    // Route pour afficher la liste des virements
    #[Route('/admin/transfers', name: 'admin_transfers')]
    public function transfers(TransferRepository $transferRepository): Response
    {
        // Récupérer les virements en fonction de leur statut
        $pendingTransfers = $transferRepository->findTransfersByStatus('En Attente');
        $approvedTransfers = $transferRepository->findTransfersByStatus('Approuvé');
        $rejectedTransfers = $transferRepository->findTransfersByStatus('Refusé');

        // Calculer le nombre de virements
        $pendingCount = count($pendingTransfers);
        $approvedCount = count($approvedTransfers);
        $rejectedCount = count($rejectedTransfers);

        return $this->render('admin/transfers.html.twig', [
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'pendingTransfers' => $pendingTransfers,
            'approvedTransfers' => $approvedTransfers,
            'rejectedTransfers' => $rejectedTransfers,
        ]);
    }    

    // Route pour approuver un virement
    #[Route("/admin/transfer/approve/{id}", name: "admin_transfer_approve", methods: ["POST"])]
    public function approveTransfer(Transfer $transfer, EntityManagerInterface $em): RedirectResponse
    {
        // Vérifier si le virement est en attente
        if ($transfer->getStatus() !== 'En Attente') {
            $this->addFlash('error', 'Ce virement n\'est pas en attente.');
            return $this->redirectToRoute('admin_transfers');
        }

        // Approuver le virement
        $transfer->setStatus('Approuvé');

        // Créditer le compte destinataire
        $toAccount = $transfer->getToAccount();
        $fromAccount = $transfer->getFromAccount();
        $amount = $transfer->getAmount();

        // Vérifier si le compte destinataire existe et si le montant est valide
        if ($toAccount && $amount > 0) {
            $toAccount->setBalance($toAccount->getBalance() + $amount);
        }

        // Débiter le compte de l'envoyeur
        $fromAccount->setBalance($fromAccount->getBalance() - $amount);

        // Mettre à jour la base de données
        $em->flush();

        $this->addFlash('success', 'Virement approuvé et effectué avec succès.');

        return $this->redirectToRoute('admin_transfers');
    }

    // Route pour rejeter un virement
    #[Route("/admin/transfer/reject/{id}", name: "admin_transfer_reject", methods: ["POST"])]
    public function rejectTransfer(Transfer $transfer, EntityManagerInterface $em): RedirectResponse
    {
        // Vérifier si le virement est en attente
        if ($transfer->getStatus() !== 'En Attente') {
            $this->addFlash('error', 'Ce virement n\'est pas en attente.');
            return $this->redirectToRoute('admin_transfers');
        }

        // Rejeter le virement
        $transfer->setStatus('Refusé');

        // Retourner l'argent au compte de l'envoyeur
        $fromAccount = $transfer->getFromAccount();
        $amount = $transfer->getAmount();

        if ($fromAccount && $amount > 0) {
            $fromAccount->setBalance($fromAccount->getBalance() + $amount);
        }

        // Mettre à jour la base de données
        $em->flush();

        $this->addFlash('success', 'Virement refusé et argent retourné à l\'envoyeur.');

        return $this->redirectToRoute('admin_transfers');
    }

    // Route pour afficher la liste des cartes
    #[Route("/admin/cards", name: "admin_cards")]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer toutes les cartes avec leur statut
        $pendingCards = $em->getRepository(CardOrder::class)->findBy(['status' => 'En Attente']);
        $approvedCards = $em->getRepository(CardOrder::class)->findBy(['status' => 'Approuvé']);
        $rejectedCards = $em->getRepository(CardOrder::class)->findBy(['status' => 'Refusé']);
    
        // Compter les cartes dans chaque catégorie
        $pendingCount = count($pendingCards);
        $approvedCount = count($approvedCards);
        $rejectedCount = count($rejectedCards);
    
        // Passer toutes les informations à la vue
        return $this->render('admin/cards.html.twig', [
            'pendingCount' => $pendingCount,
            'activeCount' => $approvedCount,
            'blockedCount' => $rejectedCount,  // Si tu as une autre catégorie pour les "bloquées"
            'cardRequests' => $pendingCards, // Affiche les demandes en attente ici
            'rejectedCards' => $rejectedCards, // Passer les cartes refusées à la vue
        ]);
    }
    

#[Route("/admin/cards/approve/{id}", name: "admin_cards_approve", methods: ["POST"])]
public function approve(CardOrder $cardOrder, EntityManagerInterface $em): RedirectResponse
{
    // Vérifie que la carte est en attente
    if ($cardOrder->getStatus() !== 'En Attente') {
        $this->addFlash('error', 'Cette carte n\'est pas en attente.');
        return $this->redirectToRoute('admin_cards');
    }

    // Approuver la carte
    $cardOrder->setStatus('Approuvé');
    $em->flush();

    $this->addFlash('success', 'Carte approuvée avec succès.');

    return $this->redirectToRoute('admin_cards');
}

#[Route("/admin/cards/reject/{id}", name: "admin_cards_reject", methods: ["POST"])]
public function reject(CardOrder $cardOrder, EntityManagerInterface $em): RedirectResponse
{
    // Vérifie que la carte est en attente
    if ($cardOrder->getStatus() !== 'En Attente') {
        $this->addFlash('error', 'Cette carte n\'est pas en attente.');
        return $this->redirectToRoute('admin_cards');
    }

    // Rejeter la carte
    $cardOrder->setStatus('Refusé');
    $em->flush();

    $this->addFlash('success', 'Carte refusée avec succès.');

    // Récupérer les cartes refusées
    $rejectedCards = $em->getRepository(CardOrder::class)->findBy(['status' => 'Refusé']);

    return $this->redirectToRoute('admin_cards', ['rejectedCards' => $rejectedCards]);
}

    // Route pour afficher la liste des prêts
    #[Route('/admin/loans', name: 'admin_loans')]
    public function loans(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $search = $request->query->get('search');
        
        // Si un terme de recherche est fourni, filtrer les demandes de prêt par nom ou prénom de l'utilisateur
        if ($search) {
            $loanRequests = $em->getRepository(LoanRequest::class)
                ->findByUserNameOrFirstName($search); // Méthode personnalisée pour la recherche
        } else {
            // Sinon, récupérer toutes les demandes de prêt par statut 'En Attente'
            $loanRequests = $em->getRepository(LoanRequest::class)->findBy(['status' => 'En Attente']);
        }
        
        // Récupérer les autres demandes (approuvées et rejetées)
        $approvedRequests = $em->getRepository(LoanRequest::class)->findBy(['status' => 'Approuvé']);
        $rejectedRequests = $em->getRepository(LoanRequest::class)->findBy(['status' => 'Rejeté']);
        
        // Compter les demandes dans chaque catégorie
        $pendingCount = count($loanRequests);  // On compte les résultats filtrés ou en attente
        $approvedCount = count($approvedRequests);
        $rejectedCount = count($rejectedRequests);
        
        // Calculer le montant total des prêts (en attente et approuvés)
        $totalAmount = array_sum(array_map(fn($request) => $request->getAmount(), array_merge($loanRequests, $approvedRequests)));
        
        // Passer toutes les informations à la vue
        return $this->render('admin/loans.html.twig', [
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'loanRequests' => $loanRequests, // Afficher les demandes en attente ou filtrées
            'rejectedRequests' => $rejectedRequests, // Passer les demandes refusées à la vue
            'totalAmount' => $totalAmount,
            'search' => $search, // Passer la valeur de la recherche pour l'afficher dans le champ
        ]);
    }
    
    
    
    #[Route('/admin/loan/{id}/approve', name: 'admin_approve_loan')]
    public function approveLoan(int $id, LoanRequestRepository $loanRequestRepository, EntityManagerInterface $em): Response
    {
        // Récupérer la demande de prêt par son ID
        $loanRequest = $loanRequestRepository->find($id);
        if (!$loanRequest) {
            throw $this->createNotFoundException('Demande de prêt non trouvée');
        }
    
        // Modifier le statut de la demande
        $loanRequest->setStatus('Approuvé');
    
        // Sauvegarder les modifications
        $em->flush();
    
        // Rediriger vers la liste des demandes
        return $this->redirectToRoute('admin_loans');
    }
    
    #[Route('/admin/loan/{id}/reject', name: 'admin_reject_loan')]
    public function rejectLoan(int $id, LoanRequestRepository $loanRequestRepository, EntityManagerInterface $em): Response
    {
        // Récupérer la demande de prêt par son ID
        $loanRequest = $loanRequestRepository->find($id);
        if (!$loanRequest) {
            throw $this->createNotFoundException('Demande de prêt non trouvée');
        }
    
        // Modifier le statut de la demande
        $loanRequest->setStatus('Rejeté');
    
        // Sauvegarder les modifications
        $em->flush();
    
        // Rediriger vers la liste des demandes
        return $this->redirectToRoute('admin_loans');
    }
    
    

    // Route pour afficher la liste des administrateurs
    #[Route('/admin/admins', name: 'admin_admins')]
    public function admins(UserRepository $userRepository): Response
    {
        // Récupérer tous les utilisateurs ayant le rôle 'ROLE_ADMIN'
        $admins = $userRepository->findBy(['roles' => 'ROLE_ADMIN']);
    
        // Calculer le total des administrateurs
        $totalAdmins = count($admins);
    
        // Filtrer les administrateurs actifs et inactifs
        $activeAdmins = array_filter($admins, fn($admin) => $admin->getIsActive());
        $inactiveAdmins = array_filter($admins, fn($admin) => !$admin->getIsActive());
    
        // Calculer le nombre d'administrateurs actifs et inactifs
        $activeCount = count($activeAdmins);
        $inactiveCount = count($inactiveAdmins);
    
        return $this->render('admin/admins.html.twig', [
            'admins' => $admins,
            'totalAdmins' => $totalAdmins,
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
        ]);
    }
}
