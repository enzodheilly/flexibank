<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Entity\Notification;
use App\Entity\Beneficiary;
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
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(TransferType::class, null, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fromAccount = $data['fromAccount'];
            $amount = $data['amount'];
            $description = $data['description'];

            // ✅ Si un bénéficiaire est sélectionné, on utilise son IBAN
            $beneficiary = $form->get('beneficiary')->getData();
            $toAccountIban = $beneficiary ? $beneficiary->getIban() : strtoupper(trim($data['toAccount']));

            $toAccount = $entityManager->getRepository(BankAccount::class)->findOneBy(['iban' => $toAccountIban]);

            if (!$toAccount) {
                $this->addFlash('error', 'L\'IBAN du destinataire est introuvable.');
                return $this->redirectToRoute('transfer_new');
            }

            if ($fromAccount === $toAccount) {
                $this->addFlash('error', 'Le compte source et le compte destinataire ne peuvent pas être identiques.');
                return $this->redirectToRoute('transfer_new');
            }

            if ($fromAccount->getBalance() < $amount) {
                $this->addFlash('error', 'Solde insuffisant pour effectuer ce virement.');
                return $this->redirectToRoute('transfer_new');
            }

            $connection = $entityManager->getConnection();
            $connection->beginTransaction();

            try {
                $fromAccount->setBalance($fromAccount->getBalance() - $amount);
                $entityManager->persist($fromAccount);

                $transfer = new Transfer();
                $transfer->setFromAccount($fromAccount);
                $transfer->setToAccount($toAccount);
                $transfer->setAmount($amount);
                $transfer->setDescription($description);
                $transfer->setStatus('En Attente');
                $entityManager->persist($transfer);

                $notification = new Notification();
                $notification->setUser($toAccount->getUser());
                $notification->setDescription(sprintf(
                    'Vous avez reçu un virement de %s de %.2f€. Description : %s',
                    $fromAccount->getUser()->getEmail(),
                    $amount,
                    $description
                ));
                $notification->setIsRead(false);
                $entityManager->persist($notification);

                $entityManager->flush();
                $connection->commit();

                $this->addFlash('success', 'Le virement a été enregistré avec succès.');
                return $this->redirectToRoute('transfer_new');
            } catch (\Exception $e) {
                $connection->rollBack();
                $this->addFlash('error', 'Erreur lors du traitement du virement : ' . $e->getMessage());
                return $this->redirectToRoute('transfer_new');
            }
        }

        return $this->render('transfer/index.html.twig', [
            'transferForm' => $form->createView(),
        ]);
    }
}
