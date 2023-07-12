<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                 'label'=> 'Email',
                 'attr'=>[
                     'placeholder'=>"Email",
                     'class'=>"form-control",
                 ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])

            ->add('name', TextType::class, [
                'label'=> 'Nom agence',
                'attr'=>[
                    'placeholder'=>"Nom et Prénoms  ",
                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],

            ])
            ->add('adresse',  TextType::class, [
                'label'=> 'Adresse',
                'attr'=>[
                    'placeholder'=>"Adresse",
                    'class'=>"form-control",
                    ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
                ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr'=>[
                    'placeholder'=>"Faite une description de votre agence",
                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],

            ])

            ->add('postal',  TextType::class, [
                'label'=> 'Code Postal',
                'attr'=>[
                    'placeholder'=>" Code Postal",
                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])
            ->add('ville',  TextType::class, [
                'label'=> 'Ville',
                'attr'=>[
                    'placeholder'=>"Ville",
                    'class'=>"form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'=>'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                    'placeholder'=>"Mot de passe",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caracters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('phone', EmailType::class, [
                'label' => 'Numéro de Téléphone',
                'attr' => [
                    'placeholder' => "Phone",
                    'class' => "form-control",
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ obligatoire',
                    ]),

                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label'=>"J'ai lu et j'accepte les CGU de E-Travel",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les CGU',
                    ]),
                ],
            ])
            ->add('photo',FileType::class, [
                    'label' => 'Logo ',

// unmapped means that this field is not associated to any entity property
                    'mapped' => false,

// make it optional so you don't have to re-upload the PDF file
// every time you edit the Product details
                    'required' => true,

// unmapped fields can't define their validation using annotations
// in the associated entity, so you can use the PHP constraint classes
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Champ obligatoire',
                        ]),

                    ],

                    'attr'=>[
                        'class'=>"form-control",
                        'accept'=> 'image/*'
                    ]
                    ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
