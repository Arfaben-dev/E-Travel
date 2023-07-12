<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Entity\Transport;
use App\Entity\User;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('citystart', TextType::class, [
                'label' => 'Destination Départ ',
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('cityend', TextType::class, [
                'label' => 'Destination Arrivée',
                'attr' => [
                    'class' => 'form-control'
                ]

            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',


            ])
            ->add('prix', TextType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'form-control'
                ]

            ])

            ->add('datestart', DateType::class, [
                'label' => 'Date départ',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'empty_data' => '',

            ])
            ->add('hourstart', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'empty_data' => '',
                'by_reference' => true,


            ])
            ->add('hourend', TimeType::class, [
                'label' => 'Heure arrivée',
                'widget' => 'single_text',
                'empty_data' => '',
                'by_reference' => true,

            ])
            ->add('placedispo', IntegerType::class, [
                'label' => 'Place disponible',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ]

            ])
            ->add('images', FileType::class, [
                'label' => 'Images ',
                'multiple' => true,
// unmapped means that this field is not associated to any entity property
                'mapped' => false,

// make it optional so you don't have to re-upload the PDF file
// every time you edit the Product details
                'required' => false,

// unmapped fields can't define their validation using annotations
// in the associated entity, so you can use the PHP constraint classes
                'attr' => [
                    'class' => "form-control",
                    'accept' => 'image/*'
                ]
            ])
            ->add('transport', EntityType::class,
                [
                    'label' => 'Moyen de Transport',
                    'class' => Transport::class,
                    'choice_label' => 'name',
                    'attr' => [
                        'class' => '    select2'
                    ]

                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
