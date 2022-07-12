<?php

namespace App\Form;

use App\Entity\ProviderService;
use App\Entity\User;
use App\Entity\Service;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProviderServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('price')
                ->add('duration')
                ->add('service', EntityType::class, [
                    'placeholder' => 'Service',
                    'class' => Service::class,
                    'choice_label' => 'name'
                    ])
                ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProviderService::class,
        ]);
    }
}
