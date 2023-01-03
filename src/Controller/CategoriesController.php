<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Property;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class CategoriesController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        $categories = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->innerJoin('c.properties', 'p')
            ->groupBy('c')
            ->having('COUNT(p) > 0')
            ->getQuery()
            ->getResult();

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/{slug}', name: 'categories_show')]
    public function show(Category $category): Response
    {
        // Récupérer les propriétés associées à la catégorie
        $properties = $category->getProperties();
        
        return $this->render('property/index.html.twig', [
            'category' => $category,
            'properties' => $properties,
        ]);
    }
}
