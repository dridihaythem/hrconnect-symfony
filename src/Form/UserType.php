<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cin')
            ->add('tel')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('password')            
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'RH' => 'RH',
                    'Stagiaire' => 'STAGIAIRE',
                    'Employé' => 'EMPLOYE',
                    'Utilisateur' => 'USER',
                    'Admin' => 'ADMIN',
                ],
                'multiple' => false,      // pour permettre plusieurs rôles
                'label' => 'Rôle',
                'required' => true,
            ]);
            // ->add('id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
