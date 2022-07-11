<?php

namespace App\Form;

use App\Entity\ProviderService;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProviderServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (in_array('ROLE_PROVIDER', $options ['role'])) {
            $builder
                ->add('price')
                ->add('duration')
                // ->add('provider')
                // ->add('service')
            ;
        }
        if (in_array('ROLE_ADMIN', $options ['role'])) {
            $builder
                ->add('price')
                ->add('duration')
                ->add('provider')
                ->add('service')
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProviderService::class,
        ]);
    }
}
