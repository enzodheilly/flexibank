<?php

namespace App\Form;

use App\Entity\BankAccount;
use App\Entity\Beneficiary;
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

            ->add('beneficiary', EntityType::class, [
                'class' => Beneficiary::class,
                'choices' => $user->getBeneficiaries(),
                'choice_label' => function (Beneficiary $b) {
                    return $b->getName() . ' (****' . substr($b->getIban(), -4) . ')';
                },
                'label' => 'Bénéficiaire enregistré',
                'placeholder' => 'Choisir un bénéficiaire',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'beneficiary-select',
                ],
            ])

            ->add('toAccount', TextType::class, [
                'label' => 'IBAN du compte destinataire',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: FR203000300459833583',
                    'id' => 'iban-field',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^FR\d{18}$/',
                        'message' => 'Le format de l\'IBAN est invalide.',
                    ]),
                ],
            ])

            ->add('amount', MoneyType::class, [
                'currency' => false,
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
