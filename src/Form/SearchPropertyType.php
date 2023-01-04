<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('mots', SearchType::class, [
            'label' => false,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Entrez un ou plusieurs mots-clés (Ville, Département, Titre, Adresse, Status etc)'
            ],
            'required' => false
        ])
        // ->add('categorie', EntityType::class, [
        //     'class' => Categories::class,
        //     'label' => false,
        //     'attr' => [
        //         'class' => 'form-control',
        //     ],
        //     'required' => false
        // ])
        ->add('Rechercher', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-b-n mt-3',
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
