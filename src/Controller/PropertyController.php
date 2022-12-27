<?php

namespace App\Controller;

use App\Entity\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/properties', name: 'property_')]
class PropertyController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $properties = $this->entityManager->getRepository(Property::class)->findAll();
        return $this->render('property/index.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Property $property, ManagerRegistry $doctrine): Response
    {
        $thisproperty = $this->entityManager->getRepository(Property::class)->findOneBy(['slug' => $property->getSlug()]);;
        $entityManager = $doctrine->getManager();
        $views = $thisproperty->getNbview();
        $moreviews = $views + 1;
        $thisproperty->setNbview($moreviews);
        $entityManager->flush();
        return $this->render('property/details.html.twig', [
            'property' => $property
        ]);
    }



}
