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

    public function load(ObjectManager $manager): void
    {
        for($ca = 0; $ca < 10 ; ++$ca){
            $faker = Faker\Factory::create('fr_FR');
            $cat = new Category();
            $cat->setName($faker->word);
            $cat->setDescription($faker->sentence);
            $cat->setSlug($this->slugger->slug($cat->getName())->lower());
            $manager->persist($cat);
            $this->addReference('cat-'.$this->counter, $cat);
            $this->counter++;
            $manager->flush();
        }
    }
}
