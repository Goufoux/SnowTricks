<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class UserType extends AbstractType
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
            ->add('avatar', Type\FileType::class, [
                'label' => 'Avatar',
                'data_class' => null,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
