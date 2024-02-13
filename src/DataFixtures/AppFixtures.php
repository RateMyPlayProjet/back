<?php

namespace App\DataFixtures;

use App\Entity\Game;
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
        $games = [];
        for ($i=0; $i<100; $i++) {
            //Instantiate new Films
            $game = new Game();
            //Handle created && updated datetime
            $created = $this->faker->dateTimeBetween("-1 week","now");
            $updated = $this->faker->dateTimeBetween($created,"now");

            //Asign Properties to Entity
            $game->setName($this->faker->name())
            ->setGenre($this->faker->word())
            ->setStatus("on")
            ->setCreateAt($created)
            ->setUpdateAt($updated);

            //stock Game entry
            $games[] = $game;
            //Add to transaction
            $manager->persist($game);

            /* foreach ($games as $gameEntry => $value) {
                $evolution = $games[array_rand($games,1)];
                if($gameEntry->getName() != $evolution->getId()) {
                    //$gameEntry->setEvolution($evolution);
                    $manager->persist($gameEntry);
                }
            } */
        }
        //Execute transaction
        $manager->flush();
    
    }
}
