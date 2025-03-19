<?php

// src/Form/CardOrderType.php
namespace App\Form;

use App\Entity\CardOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CardOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class)
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text', 
            ])
            ->add('email', EmailType::class)
            ->add('phone', TelType::class)
            ->add('cardType', ChoiceType::class, [
                'choices' => [
                    'Basic' => 'basic',
                    'Gold' => 'gold',
                    'Premium' => 'premium',
                ],
                'expanded' => false,  // false pour une liste déroulante
                'multiple' => false,  // non multiple (une seule sélection)
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CardOrder::class,
        ]);
    }
}
