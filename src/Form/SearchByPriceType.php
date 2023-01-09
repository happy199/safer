<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;

class SearchByPriceType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $minPrice = $this->entityManager->createQueryBuilder()
            ->select('MIN(p.price)')
            ->from(Property::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();

        $maxPrice = $this->entityManager->createQueryBuilder()
            ->select('MAX(p.price)')
            ->from(Property::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();

        $builder
            ->add('min_price', NumberType::class, [
                'label' => 'Prix minimum (Supérieur ou égal à : '.$minPrice.'€ )',
                'attr' => [
                    'min' => $minPrice,
                    'max' => $maxPrice,
                    'value' => 0,
                    'class' => 'form-control',
                ],
            ])
            ->add('max_price', NumberType::class, [
                'label' => 'Prix maximum (Inférieur ou égalà : '.$maxPrice.'€ )',
                'attr' => [
                    'min' => $minPrice,
                    'max' => $maxPrice,
                    'value' => 0,
                    'class' => 'form-control',
                ],
            ])
            ->add('Filter', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-b-n mt-3',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
