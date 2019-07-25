<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TrickGroup;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'label_attr' => [
                    'class' =>'text-white'
                ]
            ])
            ->add('description', Type\TextareaType::class, [
                'label_attr' => [
                    'class' =>'text-white'
                ]
            ])
            ->add('trickGroup', EntityType::class, [
                'class' => TrickGroup::class,
                'choice_label' => 'label',
                'placeholder' => 'Groupe',
                'required' => true,
                'label_attr' => [
                    'class' =>'text-white'
                ]
            ])
            ->add('media', Type\CollectionType::class, [
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'data_class' => null,
                'label' => false,
                'mapped' => false
            ])
            ->add('videoLinks', Type\CollectionType::class, [
                'entry_type' => VideoLinkType::class,
                'allow_add' => true,
                'data_class' => null,
                'label' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class
        ]);
    }
}
