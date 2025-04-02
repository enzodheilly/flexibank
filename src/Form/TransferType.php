<?php

// src/Form/TransferType.php

namespace App\Form;

use App\Entity\BankAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('fromAccount', EntityType::class, [
                'class' => BankAccount::class,
                'choice_label' => function (BankAccount $bankAccount) {
                    return $bankAccount->getAccountType() === BankAccount::ACCOUNT_TYPE_COURANT ? 'Compte courant' : 'Compte épargne';
                },
                'label' => 'Compte source',
                'query_builder' => function ($repo) use ($user) {
                    return $repo->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->andWhere('a.accountType IN (:accountTypes)')
                        ->setParameter('user', $user)
                        ->setParameter('accountTypes', [BankAccount::ACCOUNT_TYPE_COURANT, BankAccount::ACCOUNT_TYPE_EPARGNE]);
                },
                'attr' => [
                    'class' => 'form-control custom-select',
                ],
            ])
            ->add('toAccount', TextType::class, [
                'label' => 'IBAN du compte destinataire',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: FR203000300459833583',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^FR\d{18}$/',
                        'message' => 'Le format de l\'IBAN est invalide.',
                    ]),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'currency' => false,  // Désactive l'affichage du symbole €
                'label' => 'Montant',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le montant du virement',
                ],
            ])
            
            ->add('description', TextType::class, [
                'label' => 'Description du virement',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ajouter une description',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La description ne peut pas être vide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => null,
        ]);
    }
}

