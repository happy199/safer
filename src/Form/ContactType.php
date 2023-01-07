<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg form-control-a',
                    'placeholder' => 'Votre Adresse Email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un mail ',
                    ]),
                ],
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg form-control-a',
                    'placeholder' => 'Objet du message'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez entrer un objet ',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                // 'constraints' => [
                //     new NotBlank([
                //         'message' => 'Vous devez entrer un message ',
                //     ]),
                // ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
