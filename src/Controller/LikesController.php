<?php

namespace App\Controller;

use App\Entity\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class LikesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/likes/{slug}', name: 'app_likes')]
    public function index($slug, ManagerRegistry $doctrine): Response
    {
        $property = $this->entityManager->getRepository(Property::class)->findOneBy(['slug' => $slug]);;
        $entityManager = $doctrine->getManager();
        $likes = $property->getNblike();
        $morelikes = $likes + 1;
        $property->setNblike($morelikes);
        $entityManager->flush();
        return $this->redirect('/properties/'.$property->getSlug());
    }
}
