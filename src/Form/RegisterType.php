<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cin', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'CIN',
                ],
            ])
            ->add('tel', TelType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Telephone',
                ],
            ])
            ->add('nom', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('prenom', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prenom',
                ],
            ])
            ->add('email', null, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Password',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
