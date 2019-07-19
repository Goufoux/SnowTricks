<?php

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Validator\Constraints\File;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', Type\FileType::class, [
                'data_class' => null,
                // 'by_reference' => false,
                'label' => 'Image/Vidéo',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Votre fichier est trop volumineux ({{ size }}{{ suffix }}), maximum autorisé : {{ limit }}{{ suffix }}',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Merci de choisir une image PNG ou JPEG'
                    ])
                ]
                // 'mapped' => false
            ])
        ;

        if ($options['addRegister'] === true) {
            $builder->add('register', Type\SubmitType::class, [
                'label' => 'Ajouter',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
            'addRegister' => false
        ]);
    }
}
