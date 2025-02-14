<?php

// src/Form/ClientStep1Type.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Countries;

class ClientStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Liste des codes pays européens (ISO 3166-1 alpha-2)
        $europeanCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IS', 'IE', 'IT', 'LV',
            'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'CH', 'GB', 'AL', 'AD', 'BA',
            'MD', 'MC', 'ME', 'MK', 'RS', 'UA', 'SM', 'LI', 'VA'
        ];

        // Filtrer uniquement les pays européens
        $countries = array_filter(Countries::getNames(), function ($code) use ($europeanCountries) {
            return in_array($code, $europeanCountries);
        }, ARRAY_FILTER_USE_KEY);

        $builder
            ->add('paysDeResidence', ChoiceType::class, [
                'label' => 'Pays de résidence',
                'required' => true,
                'choices' => array_flip($countries), // Symfony gère les clés comme valeurs et valeurs comme labels
                'choice_attr' => function($countryCode, $countryName) {
                    return ['data-flag' => strtolower($countryCode)];
                },
                'attr' => ['class' => 'country-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
