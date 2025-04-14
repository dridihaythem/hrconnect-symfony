<?php

namespace App\Form;

use App\Entity\ParticipationSeminaire;
use App\Entity\Seminaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipationSeminaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Inscrit' => 'Inscrit',
                    'Présent' => 'présent',
                    'Absent' => 'absent',
                    'En attente' => 'en attente',
                ],
                'placeholder' => 'Choisir un statut',
            ])
            ->add('dateInscription', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(), // valeur par défaut
            ])
            ->add('evaluation')
            ->add('certificat')
            ->add('idEmploye')
            ->add('seminaire', EntityType::class, [
                'class' => Seminaire::class,
                'choice_label' => 'nomSeminaire',
                'placeholder' => 'Sélectionner un séminaire',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParticipationSeminaire::class,
        ]);
    }
}
