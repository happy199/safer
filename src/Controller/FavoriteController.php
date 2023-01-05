<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;




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
            'user'      => $user,
        ]);
    }

    #[Route("/access", name: "access")]
    public function access(): Response
    {
        return $this->render('favorite/access.html.twig');
    }    

    #[Route("/listing", name: "listing")]
    public function listing(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
    
            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'email' => $email,
                // 'roles' => [],
                'is_register' => false
            ]);

            $favorites = $this->favoriteRepository->findBy(['user' => $user]);

            if($favorites){
                return $this->render('favorite/index.html.twig', [
                    'user'      => $user,
                    'favorites' => $favorites,
                ]);
            }else{
                 return $this->render('favorite/access.html.twig');
            }  
           
        }else{
            return $this->render('favorite/access.html.twig');
        } 
    }  

    #[Route("/sendmail", name: "sendmail")]
    public function sendmail(Request $request, MailerInterface $mailer,  AuthorizationCheckerInterface $authChecker): Response
    {
        if ($request->isMethod('POST')) {
            $user_id = $request->request->get('user_id');
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' =>  $user_id]);
            $favorites = $this->favoriteRepository->findByUser($user);
            if (!empty($favorites)) {
                // Si l'utilisateur a au moins un favori, créez un email
                $email = (new TemplatedEmail())
                    ->from('contact@safer.com')
                    ->to($user->getEmail())
                    ->subject('Vos favoris')
                   // path of the Twig template to render
                    ->htmlTemplate('emails/favoritelist.html.twig')

                    // pass variables (name => value) to the template
                    ->context([
                        'favorites' => $favorites,
                        'user'      => $user
                    ]);
        
                // Envoyez l'email
                
                $mailer->send($email);  
            }

            if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                return $this->redirectToRoute('favorite_index');
            } else {
                return $this->redirectToRoute('favorite_access');
            }
        } 
    }

    #[Route("/{id}/delete", name: "delete")]
    public function delete(Favorite $favorite, AuthorizationCheckerInterface $authChecker): Response
    {
        $this->entityManager->remove($favorite);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le favori a été supprimé');

        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('favorite_index');
        } else {
            return $this->redirectToRoute('favorite_access');
        }
    }
}