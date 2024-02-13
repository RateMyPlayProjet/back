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
            //Instantiate new Films
            $film = new Films();
            //Handle created && updated datetime
            $created = $this->faker->dateTimeBetween("-1 week","now");
            $updated = $this->faker->dateTimeBetween($created,"now");

            //Asign Properties to Entity
            $film->setName($this->faker->name())
            ->setAuthor($this->faker->name())
            ->setType($this->faker->word())
            ->setDate($this->faker->date())
            ->setStatus("on")
            ->setCreateAt($created)
            ->setUpdateAt($updated);
            //Add to transaction
            $manager->persist($film);
        }
        //Execute transaction
        $manager->flush();
    
    }
}
