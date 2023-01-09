<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Favorite;
use App\Entity\Property;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractDashboardController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        // $url = $routeBuilder->setController(CategoryCrudController::class)->generateUrl();
        
        // return $this->redirect($url);
        $totalUsers = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u)')
            ->from(User::class, 'u')
            ->getQuery()
            ->getSingleScalarResult();

        $totalCategories = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c)')
            ->from(Category::class, 'c')
            ->getQuery()
            ->getSingleScalarResult();

        $totalProperties = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p)')
            ->from(Property::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();
        
        $topLikedProperties = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
            ->orderBy('p.nblike', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();    

        return $this->render('admin/my-dashboard.html.twig',[
            'totalUsers' => $totalUsers,
            'totalCategories' => $totalCategories,
            'totalProperties' => $totalProperties,
            'topLikedProperties' => $topLikedProperties,
            
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Safer');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Retour au site', 'fas fa-home', 'app_main');
        yield MenuItem::linkToRoute('Tableau de bord', 'fas fa-dashboard', 'admin');
        yield MenuItem::linkToCrud('Catégories', 'fas fa-box', Category::class);
        yield MenuItem::linkToCrud('Propriétés', 'fas fa-land-mine-on', Property::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Messages', 'fas fa-message', Contact::class);
        yield MenuItem::linkToCrud('Favoris', 'fas fa-heart', Favorite::class);
    }
}
