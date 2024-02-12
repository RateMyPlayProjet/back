<?php

namespace App\DataFixtures;

use App\Entity\Films;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     * 
     */
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void 
    {
        // $product = new Product();
        // $manager->persist($product);
        //$films = [];
        for ($i=0; $i<100; $i++) {
            $film = new Films();
            $film->setName($this->faker->name())->setAuthor($this->faker->name())->setType($this->faker->word())->setDate($this->faker->date())->setStatus($this->faker->word("on"));
            $manager->persist($film);
        }
        $manager->flush();
    }
}
