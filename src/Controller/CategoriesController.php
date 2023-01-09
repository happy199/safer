<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Form\SearchPropertyType;
use App\Form\SearchByPriceType;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

class CategoriesController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/categories', name: 'app_categories')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {

        $categoriesquery = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->innerJoin('c.properties', 'p')
            ->groupBy('c')
            ->having('COUNT(p) > 0')
            ->getQuery()
            ->getResult();

        $categories = $paginator->paginate(
            $categoriesquery, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            6 // Nombre de résultats par page
        );
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/{slug}', name: 'categories_show')]
    public function show(Category $category, PropertyRepository $propertyrepo, Request $request, PropertyService $propertyService): Response
    {
        $form = $this->createForm(SearchPropertyType::class);
        
        $search = $form->handleRequest($request);

        $formPrice = $this->createForm(SearchByPriceType::class);

        $formPrice->handleRequest($request);

        // Récupérer les propriétés associées à la catégorie
        $properties =  $propertyService->getPaginatedProperties($category);

        if($form->isSubmitted() && $form->isValid()){
            // On recherche les annonces correspondant aux mots clés
            $properties = $propertyrepo->search(
                $search->get('mots')->getData(),$category
            );
        }

        if ($formPrice->isSubmitted() && $formPrice->isValid()) {
            $data = $formPrice->getData();
        
            $minPrice = $data['min_price'];
            $maxPrice = $data['max_price'];
        
            $properties = $propertyrepo->findByPriceRange($minPrice, $maxPrice);
        }
        
        return $this->render('property/index.html.twig', [
            'category' => $category,
            'properties' => $properties,
            'form' => $form->createView(),
            'formPrice' => $formPrice->createView()
        ]);
    }

}
