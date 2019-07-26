<?php

namespace App\Form;

use App\Entity\VideoLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;

class VideoLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', Type\TextareaType::class, [
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'Coller la balise \'IFRAME\' de la vidéo ici'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VideoLink::class,
        ]);
    }
}
