<?php

// src/Form/CardOrderType.php
namespace App\Form;

use App\Entity\CardOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CardOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userEmail', EmailType::class, [
                'disabled' => true, // Le champ email est désactivé
                'attr' => ['readonly' => true]
            ])
            ->add('cardType', ChoiceType::class, [
                'choices' => [
                    'Basique' => 'basique',
                    'Gold' => 'gold',
                    'Premium' => 'premium',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Commander la carte']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CardOrder::class,
        ]);
    }
}

