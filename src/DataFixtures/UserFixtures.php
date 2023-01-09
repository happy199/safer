<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface as SluggerInterface;
use Faker;


class UserFixtures extends AppFixtures
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // Créer 5 users avec un role admin
        for($adm = 0; $adm < 5 ; ++$adm){
            $admin = new User();
            $admin->setEmail($faker->email);
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword(
                $this->passwordEncoder->hashPassword($admin, '123456')
            );
            $manager->persist($admin);
        }
        // Créer 5 users avec un role user
        for($usr = 0; $usr < 5 ; ++$usr){
            $user = new User();
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, '123456')
            );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
