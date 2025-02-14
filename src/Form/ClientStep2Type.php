<?php

// src/Form/ClientStep2Type.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientStep2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profession', ChoiceType::class, [
                'label' => 'Profession',
                'required' => true,
                'choices' => [
                    'Administration' => 'administration',
                    'Auto-Entrepreneur' => 'auto-entrepreneur',
                    'Agriculture & Agroalimentaire' => 'agriculture & agroalimentaire',
                    'Éducation & Recherche' => 'éducation & recherche',
                    'Industrie & Fabrication' => 'industrie & fabrication',
                    'Bâtiment & Travaux Publics' => 'bâtiment & travaux publics',
                    'Commerce & Distribution' => 'commerce & distribution',
                    'Finance & Assurance' => 'finance & assurance',
                    'Informatique & Télécommunications' => 'informatique & télécommunications',
                    'Médical & Santé' => 'médical & santé',
                    'Transport & Logistique' => 'transport & logistique',
                    'Tourisme & Hôtellerie' => 'tourisme & hôtellerie',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez votre profession', // Optionnel : un texte à afficher avant la sélection
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
