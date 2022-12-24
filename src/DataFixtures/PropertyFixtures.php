<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface as SluggerInterface;
use Faker;


class PropertyFixtures extends AppFixtures
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($pro = 0; $pro < 10 ; ++$pro){
            $prop = new Property();
            $prop->setTitle($faker->words(3, true));
            $prop->setDescription($faker->text());
            $prop->setSurface($faker->randomFloat(2));
            $prop->setStatus($faker->randomElement(['rent', 'sell']));
            $prop->setPrice($faker->randomNumber(6, true));
            $prop->setAddress($faker->streetAddress);
            $prop->setCity($faker->city());
            $prop->setPostalcode(intval($faker->postcode()));
            $prop->setDepartment($faker->citySuffix());
            $prop->setNblike($faker->randomNumber(3, false));
            $prop->setNbview($faker->randomNumber(3, false));
            $prop->setImages($faker->imageUrl(640, 480, 'animals', true));
            $prop->setSlug($this->slugger->slug($prop->getTitle())->lower());
            $manager->persist($prop);   
        }

        $manager->flush();
    }
}
