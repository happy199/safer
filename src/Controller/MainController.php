<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $mostLikedProperties = $this->entityManager->getRepository(Property::class)
        ->findBy([], ['nblike' => 'DESC'], 3);

        $randomProperties = $this->entityManager->getRepository(Property::class)->findBy([], ['created_at' => 'DESC'], 4, 0);

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
            ->from(Category::class, 'c')
            ->leftJoin('c.properties', 'p')
            ->where($qb->expr()->isNotNull('p.id'))
            ->orderBy('c.created_at', 'DESC')
            ->setMaxResults(4)
            ->setFirstResult(0);
        
        $randomCategories = $qb->getQuery()->getResult();
        
        return $this->render('main/index.html.twig', [
            'most_liked_properties' => $mostLikedProperties,
            'random_properties' => $randomProperties,
            'random_categories' => $randomCategories ,
        ]);
    }
    
}
