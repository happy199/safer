<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\User;
use App\Entity\Favorite;
use App\Form\FavoriteType;
use App\Repository\PropertyRepository;
use App\Service\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/properties', name: 'property_')]
class PropertyController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/', name: 'index')]
    public function index(PropertyService $propertyService, Request $request): Response
    {
        $properties = $propertyService->getPaginatedProperties();
        return $this->render('property/index.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/{slug}', name: 'details')]
    public function details(Property $property, ManagerRegistry $doctrine): Response
    {
        // dd($property);
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

    #[Route('/{slug}/favorite', name: 'favorite')]
    public function favorite(Request $request, Property $property, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $user = $this->getUser();

        if ($user) {
            // Si l'utilisateur est connecté, on vérifie s'il a déjà mis cette propriété en favori
            $favorite = $this->entityManager
                ->getRepository(Favorite::class)
                ->findOneBy(['user' => $user, 'property' => $property]);
    
            if (!$favorite) {
                // Si la propriété n'est pas déjà en favori, on crée un nouvel objet Favorite
                $favorite = new Favorite();
                $favorite->setUser($user);
                $favorite->setProperty($property);
                $property->setNblike($property->getNblike() + 1);
                $favorite->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $entityManager->persist($property);
                $entityManager->persist($favorite);
                $entityManager->flush();
            }
            $this->addFlash('success', 'La propriété a été ajoutée à vos favoris');
        }else{
            if ($request->isMethod('POST')) {
                $email = $request->request->get('email');
        
                // Si l'utilisateur a un compte, on récupère son entité User
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
                // Si l'utilisateur n'a pas de compte, on crée un nouvel utilisateur
                if (!$user) {
                    $user = new User();
                    $user->setEmail($email);
                    // On ne définit pas de mot de passe pour l'utilisateur
                    $user->setPassword('');

                    $entityManager->persist($user);
                    $entityManager->flush();
                }
        
                // On crée une nouvelle entité Favorite avec la propriété et l'utilisateur
                $favorite = new Favorite();
                $favorite->setProperty($property);
                $favorite->setUser($user);
                $favorite->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
                $property->setNblike($property->getNblike() + 1);
                $entityManager->persist($property);
                $entityManager->persist($favorite);
                $entityManager->flush();
        
                // On affiche un message de confirmation
                $this->addFlash('success', 'La propriété a été ajoutée à vos favoris');
            }
        } 

        return $this->redirectToRoute('property_details', ['slug' => $property->getSlug()]);
    }

    #[Route('/searchbyprice', name: 'searchByPrice')]
    public function searchByPrice(Request $request)
    {
        // Récupération des valeurs du range du formulaire
        $minPrice = $request->request->get('min_price');
        $maxPrice = $request->request->get('max_price');

        // Récupération des propriétés dont le prix est compris entre minPrice et maxPrice
        $properties = $this->entityManager
            ->getRepository(Property::class)
            ->findByPriceRange($minPrice, $maxPrice);

        // Rendu de la vue qui affiche les propriétés trouvées
        return $this->render('property/index.html.twig', [
            'properties' => $properties,
        ]);
    }
}
