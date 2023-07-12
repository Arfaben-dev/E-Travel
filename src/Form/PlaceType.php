<?php

namespace App\Form;

use App\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom', TextType::class, [
                'label'=> 'Nom et Prénom',
                'attr'=>[

                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],

            ])

            ->add('email', EmailType::class, [
                'label'=> 'Email',
                'attr'=>[
                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])


            ->add('phone', EmailType::class, [
                'label' => 'Numéro de Téléphone',
                'attr' => [
                    'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
