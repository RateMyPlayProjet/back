<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Plateforme;
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
        $gamesEntries = [];
        for ($i=0; $i<100; $i++) {
            //Instantiate new Films
            $game = new Game();
            //Handle created && updated datetime
            $created = $this->faker->dateTimeBetween("-1 week","now");
            $updated = $this->faker->dateTimeBetween($created,"now");

            //Asign Properties to Entity
            $game->setName($this->faker->name())
            ->setGenre($this->faker->word())
            ->setDescription($this->faker->word())
            ->setDateSortie($updated)
            ->setStatus("on")
            ->setCreateAt($created)
            ->setNbJoueurs($this->faker->numberBetween(1,10))
            ->setUpdateAt($updated);


            //stock Game entry
            $gamesEntries[] = $game;
            
            //Add to transaction
            $manager->persist($game);

            $plateformes = [];
            for ($j= 0; $j< 100; $j++) {
                $plateforme = new Plateforme();
                $plateforme->setName($game);
                $plateformes[] = $plateforme;
                $manager->persist($plateforme);
            }
            

            foreach ($gamesEntries as $key => $gameEntry) {
                $plateformesID = $plateformes[array_rand($plateforme,1)];
                $gameEntry->addPlateforme($plateformesID);
                $manager->persist($gameEntry);
            
            }
        }
        //Execute transaction
        $manager->flush();
    
    }
}
