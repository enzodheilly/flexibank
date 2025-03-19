<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Form\TransferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    #[Route('/transfer/new', name: 'transfer_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérification utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Créer le formulaire de virement en passant l'utilisateur pour filtrer les comptes
        $transferForm = $this->createForm(TransferType::class, null, [
            'user' => $user,  // Passer l'utilisateur à travers les options
        ]);
        $transferForm->handleRequest($request);
    
        if ($transferForm->isSubmitted() && $transferForm->isValid()) {
            // Traitement du formulaire de virement
            $data = $transferForm->getData();
            $fromAccount = $entityManager->getRepository(BankAccount::class)->find($data['fromAccount']);
            $toAccountIban = strtoupper(trim($data['toAccount']));  // Ajout de strtoupper et trim
            $amount = $data['amount'];
    
            // Vérification des comptes et du montant
            if (!$fromAccount) {
                $this->addFlash('error', 'Erreur : Le compte source est introuvable');
                return $this->redirectToRoute('transfer_new');
            }
    
            // Vérification de l'existence de l'IBAN du destinataire dans la base de données
            $toAccount = $entityManager->getRepository(BankAccount::class)->findOneBy(['iban' => $toAccountIban]);

            if (!$toAccount) {
                $this->addFlash('error', 'Erreur : L\'IBAN du destinataire n\'existe pas dans la base de données (IBAN: ' . $toAccountIban . ')');
                return $this->redirectToRoute('transfer_new');
            }
    
            // Vérification si les comptes source et destinataire sont identiques
            if ($fromAccount === $toAccount) {
                $this->addFlash('error', 'Erreur : Le compte source et le compte destinataire ne peuvent être identiques');
            } elseif ($fromAccount->getBalance() < $amount) {
                $this->addFlash('error', 'Erreur : Solde insuffisant');
            } else {
                // Démarrer une transaction
                $entityManager->beginTransaction();
                try {
                    // Débiter le compte source
                    $fromAccount->setBalance($fromAccount->getBalance() - $amount);
    
                    // Créditer le compte de destination
                    $toAccount->setBalance($toAccount->getBalance() + $amount);
    
                    // Créer un objet Transfer et l'enregistrer
                    $transfer = new Transfer();
                    $transfer->setFromAccount($fromAccount);
                    $transfer->setToAccount($toAccount);
                    $transfer->setAmount($amount);
                    $transfer->setDescription($data['description']); // Assure-toi que le formulaire contient un champ 'description'
    
                    // Enregistrer le virement et les changements de solde
                    $entityManager->persist($transfer);
                    $entityManager->flush();
    
                    // Commit de la transaction
                    $entityManager->commit();
    
                    // Message flash de succès
                    $this->addFlash('success', 'Virement effectué avec succès');
                } catch (\Exception $e) {
                    // En cas d'erreur, annuler la transaction
                    $entityManager->rollback();
                    $this->addFlash('error', 'Erreur : Le virement a échoué. Veuillez réessayer. ' . $e->getMessage()); // Affiche l'erreur détaillée
                }
            }
    
            // Rediriger vers la page de virement après soumission
            return $this->redirectToRoute('transfer_new');
        }
    
        // Rendu de la vue avec le formulaire de virement
        return $this->render('transfer/index.html.twig', [
            'transferForm' => $transferForm->createView(),
        ]);
    }
}
