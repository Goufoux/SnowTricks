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
                'required' => true
            ])
            ->add('description', Type\TextareaType::class)
            ->add('trickGroup', EntityType::class, [
                'class' => TrickGroup::class,
                'choice_label' => 'label',
                'placeholder' => 'Group ?',
                'required' => true
            ])
            ->add('media', Type\CollectionType::class, [
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                // 'by_reference' => false,
                'data_class' => null
            ])
            ->add('register', Type\SubmitType::class, [
                'label' => ($options['forAdd'] === true) ? 'Ajouter' : 'Mettre à jour',
                'attr' => [
                    'class' => ($options['forAdd'] === true) ? 'btn btn-success' : 'btn btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'forAdd' => true
        ]);
    }
}
