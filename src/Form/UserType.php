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
        $builder
            ->add('telephone')
            ->add('roles')
            ->add('password')
            ->add('firstname')
            ->add('lastname')
            ->add('companyName')
            ->add('email')
            ->add('town')
            ->add('district')
            ->add('image')
            ->add('isTop')
            ->add('isArchived')
            ->add('companyDescription')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
