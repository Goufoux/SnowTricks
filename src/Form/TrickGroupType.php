<?php

namespace App\Form;

use App\Entity\TrickGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class TrickGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('register', Type\SubmitType::class, [
                'attr' => [
                    'class' => ($options['forAdd'] === true) ? 'btn btn-success' : 'btn btn-info'
                ],
                'label' => ($options['forAdd'] === true) ? 'Ajouter' : 'Mettre à jour'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrickGroup::class,
            'forAdd' => true
        ]);
    }
}
