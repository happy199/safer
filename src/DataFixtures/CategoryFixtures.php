<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface as SluggerInterface;
use Faker;

class CategoryFixtures extends Fixture  
{
    private $counter = 1;
    public function __construct(private SluggerInterface $slugger){}

     /**
     * Fonction de chargement des fixtures de catégories.
     */

    public function load(ObjectManager $manager): void
    {
        // Boucle de création de 10 catégories

        for($ca = 0; $ca < 10 ; ++$ca){
            // Utilisation de la librairie Faker pour générer des données aléatoires
            $faker = Faker\Factory::create('fr_FR');
            $cat = new Category();
            $cat->setName($faker->word);
            $cat->setDescription($faker->sentence);
            $cat->setSlug($this->slugger->slug($cat->getName())->lower());
            $manager->persist($cat);
            $this->addReference('cat-'.$this->counter, $cat);
            $this->counter++;
            // Envoi en base de données des données persistées
            $manager->flush();
        }
    }
}
