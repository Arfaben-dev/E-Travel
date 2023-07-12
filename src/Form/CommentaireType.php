<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note',  ChoiceType::class, [
                'choices' => [
                    '1' ,
                    '2' ,
                    '3' ,
                    '4' ,
                    '5' ,
                    '5' ,
                ]
            ])
            ->add('nom', TextareaType::class, [
                'label' => 'Commentaire ',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
