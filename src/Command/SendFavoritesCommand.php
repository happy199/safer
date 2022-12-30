<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\FavoriteRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class SendFavoritesCommand extends Command
{
    protected static $defaultName = 'app:send-favorites';

    private $mailer;
    private $parameterBag;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $parameterBag)
    {
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Envoie la liste des favoris par email aux utilisateurs qui ont un compte.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Récupère la liste des utilisateurs ayant au moins un favori
        $users = $this->entityManager->getRepository(User::class)->findUsersWithFavorites();

        // Pour chaque utilisateur, envoie un email avec la liste de ses favoris
        foreach ($users as $user) {
            $favorites = $this->favoritesRepository->findByUser($user);
            $favoriteProperties = [];
            foreach ($favorites as $favorite) {
                $favoriteProperties[] = $favorite->getProperty()->getTitle();
            }
            $message = (new Email())
                ->From($this->mailerFrom)
                ->to($user->getEmail())
                ->subject('Vos favoris')
                ->text('Voici la liste de vos favoris :')
                ->html(
                    $this->twig->render(
                        'emails/favorites.html.twig',
                        ['favorites' => $favorites]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }

        $output->writeln('Emails envoyés avec succès');
    }
}