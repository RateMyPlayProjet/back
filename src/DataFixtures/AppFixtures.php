<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\Persona;
use App\Entity\Plateforme;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $userPasswordHasher;
    /**
     * @var Generator
     * 
     */
    private Generator $faker;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void 
    {
        $personas = [];
            for ($i=0; $i < 10; $i++) { 
            $gender = random_int( 0, 1);
        $genderStr = $gender ? 'male' : "female";
        $persona = new Persona();
        $birthdateStart =  new \DateTime("01/01/1980");
        $birthdateEnd = new \DateTime("01/01/2000");
        $birthDate = $this->faker->dateTimeBetween($birthdateStart,$birthdateEnd);
           $created = $this->faker->dateTimeBetween("-1 week", "now");
            $updated = $this->faker->dateTimeBetween($created, "now");
        $persona
        ->setPhone($this->faker->e164PhoneNumber())
        ->setGender($gender)
        ->setName($this->faker->lastName($genderStr))
        ->setSurname($this->faker->firstName($genderStr))
        ->setEmail($this->faker->email())
        ->setBirthdate( $birthDate)
        ->setAnonymous(false)
        ->setStatus("on")
        ->setCreatedAt($created)
        ->setUpdatedAt($updated);

        $manager->persist($persona);
        $personas[] = $persona;
        }

        $users = [];

        //Set Public User
        $publicUser = new User();
        $publicUser->setUsername("public");
        $publicUser->setRoles(["PUBLIC"]);
        $publicUser->setPassword($this->userPasswordHasher->hashPassword($publicUser, "public"));
        $publicUser->setPersona($personas[array_rand($personas, 1)]);
        $manager->persist($publicUser);
        $users[] = $publicUser;


        for ($i = 0; $i < 5; $i++) {
            $userUser = new User();
            $password = $this->faker->password(2, 6);
            $userUser->setUsername($this->faker->userName() . "@". $password);
            $userUser->setRoles(["USER"]);
            $userUser->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
            $userUser->setPersona($personas[array_rand($personas, 1)]);

            $manager->persist($userUser);
            $users[] = $userUser;
        }

            // Admins
        $adminUser = new User();
        $adminUser->setUsername("admin");
        $adminUser->setRoles(["ADMIN"]);
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, "password"));
        $adminUser->setPersona($personas[array_rand($personas, 1)]);
        $manager->persist($adminUser);
        $users[] = $adminUser;

            

        
        
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
                $plateformesID = $plateformes[array_rand($plateformes,1)];
                $gameEntry->addPlateforme($plateformesID);
                $manager->persist($gameEntry);
            
            }
        }
        //Execute transaction
        $manager->flush();
    
    }
}
