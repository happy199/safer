<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Form\SearchPropertyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function show(Category $category, PropertyRepository $propertyrepo, Request $request): Response
    {
        $form = $this->createForm(SearchPropertyType::class);
        
        $search = $form->handleRequest($request);

        // Récupérer les propriétés associées à la catégorie
        $properties = $category->getProperties();

        if($form->isSubmitted() && $form->isValid()){
            // On recherche les annonces correspondant aux mots clés
            $properties = $propertyrepo->search(
                $search->get('mots')->getData(),$category
            );
        }
        
        return $this->render('property/index.html.twig', [
            'category' => $category,
            'properties' => $properties,
            'form' => $form->createView()
        ]);
    }
}
