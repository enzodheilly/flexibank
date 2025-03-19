<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class LoanRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Montant du prêt
            ->add('amount', NumberType::class, [
                'label' => 'Montant du prêt (€)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive(),
                ],
            ])
            // Durée du prêt
            ->add('duration', NumberType::class, [
                'label' => 'Durée (mois)',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 1, 'max' => 360]),
                ],
            ])
            // Choix du type de prêt
            ->add('loanType', ChoiceType::class, [
                'label' => 'Type de prêt',
                'choices' => [
                    'Projet Personnel' => 'projet_personnel',
                    'Achat de Véhicule' => 'vehicule',
                    'Travaux / Rénovation' => 'travaux',
                    'Études / Formation' => 'etudes',
                    'Voyage' => 'voyage',
                    'Équipement Maison' => 'equipement_maison',
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => true,
            ])
            // Profession de l'utilisateur
            ->add('profession', TextType::class, [
                'label' => 'Votre profession',
            ]);
    }
}
