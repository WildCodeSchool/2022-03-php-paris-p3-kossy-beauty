<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Display common fields for every users
        $builder
            ->add('telephone')
            // ->add('password')
            ->add('firstname')
            ->add('lastname')
            ->add('email')
            ->add('town')
            ->add('district')
            ->add('image')
        ;

        // Display additionnal fields for a specific role
        if (in_array('ROLE_ADMIN', $options['role'])) {
            $builder
                ->add('roles')
                ->add('isTop')
                ->add('isArchived')
            ;
        }
        if (in_array('ROLE_PROVIDER', $options['role'])) {
            $builder
                ->add('companyName')
                ->add('companyDescription')
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'role' => ['ROLE_USER']
        ]);
    }
}
