<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class UpdateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Nom'
            ])
            ->add('firstName', Type\TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('email', Type\EmailType::class, [
                'label' => 'Email'
            ])
            ->add('file', Type\FileType::class, [
                'label' => 'Avatar',
                'data_class' => null,
                'required' => false
            ]);
            if ($options['isAdmin'] === true) {
                $builder->add('roles', Type\CheckboxType::class, [
                    'label' => 'ROLE_ADMIN',
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'checked' => $options['hasRoleAdmin']
                    ]
                ]);
            }
        $builder->add('register', Type\SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info'
                ],
                'label' => 'Mettre à jour'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isAdmin' => false,
            'hasRoleAdmin' => false    
        ]);
    }
}
