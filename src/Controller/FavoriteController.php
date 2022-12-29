<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;



#[Route("/favorites", name: "favorite_")]
class FavoriteController extends AbstractController
{
    private $favoriteRepository;
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->entityManager = $entityManager;

    }

    #[Route("/", name: "index")]
    public function index(): Response
    {
        $user = $this->getUser();
        $favorites = $this->favoriteRepository->findBy(['user' => $user]);

        return $this->render('favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }

    #[Route("/{id}/delete", name: "delete")]
    public function delete(Favorite $favorite): Response
    {
        $this->entityManager->remove($favorite);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le favori a été supprimé');

        return $this->redirectToRoute('favorite_index');
    }
}