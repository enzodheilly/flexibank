<?php

// src/Controller/ClientController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\BankAccount;
use App\Form\ClientStep1Type;
use App\Form\ClientStep2Type;
use App\Form\ClientStep3Type;
use App\Form\ClientStep4Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client/step1', name: 'client_step1')]
    public function step1(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        $form = $this->createForm(ClientStep1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('client_step2');
        }

        return $this->render('client/step1.html.twig', [
            'form' => $form->createView(),
            'step' => 1,
        ]);
    }

    #[Route('/client/step2', name: 'client_step2')]
    public function step2(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        $form = $this->createForm(ClientStep2Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('client_step3');
        }

        return $this->render('client/step2.html.twig', [
            'form' => $form->createView(),
            'step' => 2,
        ]);
    }

    #[Route('/client/step3', name: 'client_step3')]
    public function step3(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        $form = $this->createForm(ClientStep3Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('client_step4');
        }

        return $this->render('client/step3.html.twig', [
            'form' => $form->createView(),
            'step' => 3,
        ]);
    }

// src/Controller/ClientController.php

#[Route('/client/step4', name: 'client_step4')]
public function step4(Request $request, EntityManagerInterface $entityManager)
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_register');
    }

    $form = $this->createForm(ClientStep4Type::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $roles = $user->getRoles();
        $roles[] = 'ROLE_CLIENT';
        $user->setRoles(array_unique($roles));

        // Création du compte courant avec un IBAN valide
        $checkingAccount = new BankAccount();
        $checkingAccount->setIban($checkingAccount->generateIban()) // Utilisation de la méthode generateIban
            ->setAccountType('Courant')
            ->setUser($user)
            ->setBalance(10000); // Ajout du solde de 10 000 euros

        // Création du compte épargne avec un IBAN valide
        $savingsAccount = new BankAccount();
        $savingsAccount->setIban($savingsAccount->generateIban()) // Utilisation de la méthode generateIban
            ->setAccountType('Epargne')
            ->setUser($user)
            ->setBalance(10000); // Ajout du solde de 10 000 euros

        // Enregistrement des comptes et de l'utilisateur
        $entityManager->persist($checkingAccount);
        $entityManager->persist($savingsAccount);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    return $this->render('client/step4.html.twig', [
        'form' => $form->createView(),
        'step' => 4,
    ]);
}

}
