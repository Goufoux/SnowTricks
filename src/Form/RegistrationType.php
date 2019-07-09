<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Nom'
            ])
            ->add('first_name', Type\TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Email'
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe']
            ])
            ->add('register', Type\SubmitType::class, [
                'attr' => [
                    'class' => ($options['forAdd'] === true) ? 'btn btn-success' : 'btn btn-info'
                ],
                'label' => ($options['forAdd'] === true) ? 'Inscription' : 'Mettre à jour'
            ])
        ;

        if ($options['isAdmin'] === true) {
            $builder->add('roles', Type\CheckboxType::class, [
                'label' => 'ROLE_ADMIN',
                'required' => false,
                'mapped' => false,
                'attr' => ['checked' => ($options['hasRoleAdmin'] === true) ? 'checked' : '']
            ]);
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'forAdd' => true,
            'isAdmin' => false,
            'hasRoleAdmin' => false
        ]);
    }
}
