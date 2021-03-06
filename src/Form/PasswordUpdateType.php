<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', Type\PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'invalid_message' => 'Mot de incorrect'
            ])
            ->add('password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Nouveau mot de passe'],
                'second_options' => ['label' => 'Confirmation du nouveau mot de passe'],
                'invalid_message' => 'Mot de passe invalide'
            ])
            ->add('register', Type\SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr' => [
                    'class' => 'btn btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
