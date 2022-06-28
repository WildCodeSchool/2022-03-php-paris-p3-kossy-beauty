<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Display common fields for every users
        $builder
            ->add('telephone')
            //->add('password')
            ->add('firstname', TextType::class, ['label' => 'Prénom'])
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('town', TextType::class, ['label' => 'Ville'])
            ->add('district', TextType::class, ['label' => 'Quartier'])
            //->add('image')
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
                ->add('companyName', TextType::class, ['label' => 'Entreprise'])
                ->add('companyDescription', TextareaType::class, [
                    'label' => 'Description',
                    'attr' => ['cols' => '46', 'rows' => '5']
                ]);
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
