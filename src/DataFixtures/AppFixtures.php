<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Avis;
use App\Entity\Game;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Notice;
use App\Entity\Persona;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Plateforme;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
        for ($i=0; $i < 5; $i++) { 
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
        $plateformesEntries = [];
        $noticesEntries = [];
        $categoriesEntries = [];
        
        // Créer et persister des objets de jeu manuellement
        $game1 = new Game();
        $game1->setName("The Witcher 3: Wild Hunt")
            ->setDescription("Un RPG d'action épique avec une histoire immersive.")
            ->setGenre(["RPG"])
            ->setDateSortie(new \DateTime("2015-05-19"))
            ->setStatus("on")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime())
            ->setNbJoueurs("Solo");

        $game2 = new Game();
        $game2->setName("Grand Theft Auto V")
            ->setDescription("Un jeu d'action-aventure en monde ouvert.")
            ->setGenre(["Action", "Aventure"])
            ->setDateSortie(new \DateTime("2013-09-17"))
            ->setStatus("on")
            ->setNbJoueurs("Multijoueur en ligne")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game3 = new Game();
        $game3->setName("The Legend of Zelda: Breath of the Wild")
            ->setDescription("Un jeu d'aventure en monde ouvert.")
            ->setGenre(["Action", "Aventure"])
            ->setDateSortie(new \DateTime("2017-03-03"))
            ->setStatus("on")
            ->setNbJoueurs("Solo")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game4 = new Game();
        $game4->setName("Red Dead Redemption 2")
            ->setDescription("Un jeu d'action-aventure en monde ouvert.")
            ->setGenre(["Action", "Aventure"])
            ->setDateSortie(new \DateTime("2018-10-26"))
            ->setStatus("on")
            ->setNbJoueurs("Multijoueur en ligne")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game5 = new Game();
        $game5->setName("Final Fantasy VII Remake")
            ->setDescription("Un jeu de rôle (RPG) avec des éléments d'action.")
            ->setGenre(["RPG"])
            ->setDateSortie(new \DateTime("2020-04-10"))
            ->setStatus("on")
            ->setNbJoueurs("Solo")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game6 = new Game();
        $game6->setName("The Last of Us Part II")
            ->setDescription("Un jeu d'action-aventure et de survie.")
            ->setGenre(["Action", "Aventure"])
            ->setDateSortie(new \DateTime("2020-06-19"))
            ->setStatus("on")
            ->setNbJoueurs("Solo")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
    ->setUpdatedAt(new \DateTime());

        $game7 = new Game();
        $game7->setName("Cyberpunk 2077")
            ->setDescription("Un RPG d'action en monde ouvert.")
            ->setGenre(["RPG", "Action"])
            ->setDateSortie(new \DateTime("2020-12-10"))
            ->setStatus("on")
            ->setNbJoueurs("Solo")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game8 = new Game();
        $game8->setName("Assassin's Creed Valhalla")
            ->setDescription("Un jeu d'action-aventure basé sur l'exploration et le combat.")
            ->setGenre(["Action", "Aventure"])
            ->setDateSortie(new \DateTime("2020-11-10"))
            ->setStatus("on")
            ->setNbJoueurs("Solo")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game9 = new Game();
        $game9->setName("Minecraft")
            ->setDescription("Un jeu de construction, de survie et de créativité.")
            ->setGenre(["Sandbox"])
            ->setDateSortie(new \DateTime("2011-11-18"))
            ->setStatus("on")
            ->setNbJoueurs("Multijoueur en ligne")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game10 = new Game();
        $game10->setName("League of Legends")
            ->setDescription("Un jeu en ligne multijoueur de type arène de bataille en ligne (MOBA).")
            ->setGenre(["MOBA"])
            ->setDateSortie(new \DateTime("2009-10-27"))
            ->setStatus("on")
            ->setNbJoueurs("Multijoueur en ligne")
            ->setCreatedAt(new \DateTime()) // Définir la date de création
            ->setUpdatedAt(new \DateTime());

        $game11 = new Game();
        $game11->setName("Super Mario Odyssey")
                ->setDescription("Un jeu de plateforme d'aventure en monde ouvert.")
                ->setGenre(["Plateforme", "Aventure"])
                ->setDateSortie(new \DateTime("2017-10-27"))
                ->setStatus("on")
                ->setNbJoueurs("Solo")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game12 = new Game();
        $game12->setName("FIFA 22")
                ->setDescription("Un jeu de simulation de football.")
                ->setGenre(["Simulation", "Sport"])
                ->setDateSortie(new \DateTime("2021-10-01"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game13 = new Game();
        $game13->setName("Call of Duty: Warzone")
                ->setDescription("Un jeu de tir à la première personne en mode bataille royale.")
                ->setGenre(["FPS", "Action"])
                ->setDateSortie(new \DateTime("2020-03-10"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game14 = new Game();
        $game14->setName("Animal Crossing: New Horizons")
                ->setDescription("Un jeu de simulation de vie et de gestion.")
                ->setGenre(["Simulation"])
                ->setDateSortie(new \DateTime("2020-03-20"))
                ->setStatus("on")
                ->setNbJoueurs("Solo")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game15 = new Game();
        $game15->setName("Pokémon Épée et Bouclier")
                ->setDescription("Un RPG d'aventure Pokémon.")
                ->setGenre(["RPG"])
                ->setDateSortie(new \DateTime("2019-11-15"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game16 = new Game();
        $game16->setName("Overwatch")
                ->setDescription("Un jeu de tir multijoueur en équipe.")
                ->setGenre(["FPS", "Action"])
                ->setDateSortie(new \DateTime("2016-05-24"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game17 = new Game();
        $game17->setName("The Elder Scrolls V: Skyrim")
                ->setDescription("Un RPG en monde ouvert avec une grande liberté d'exploration.")
                ->setGenre(["RPG"])
                ->setDateSortie(new \DateTime("2011-11-11"))
                ->setStatus("on")
                ->setNbJoueurs("Solo")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game18 = new Game();
        $game18->setName("Fortnite")
                ->setDescription("Un jeu de bataille royale multijoueur.")
                ->setGenre(["Action"])
                ->setDateSortie(new \DateTime("2017-07-25"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game19 = new Game();
        $game19->setName("Super Smash Bros. Ultimate")
                ->setDescription("Un jeu de combat mettant en scène des personnages de différents univers.")
                ->setGenre(["Combat"])
                ->setDateSortie(new \DateTime("2018-12-07"))
                ->setStatus("on")
                ->setNbJoueurs("Multijoueur local et en ligne")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
        
        $game20 = new Game();
        $game20->setName("Death Stranding")
                ->setDescription("Un jeu d'action-aventure en monde ouvert.")
                ->setGenre(["Action", "Aventure"])
                ->setDateSortie(new \DateTime("2019-11-08"))
                ->setStatus("on")
                ->setNbJoueurs("Solo")
                ->setCreatedAt(new \DateTime()) // Définir la date de création
                ->setUpdatedAt(new \DateTime());
            
        $gamesEntries = [
            $game1, $game2, $game3, $game4, $game5,
            $game6, $game7, $game8, $game9, $game10,
            $game11, $game12, $game13, $game14, $game15,
            $game16, $game17, $game18, $game19, $game20
        ];

        // Persistez les objets de jeu
        $manager->persist($game1);
        $manager->persist($game2);
        $manager->persist($game3);
        $manager->persist($game4);
        $manager->persist($game5);
        $manager->persist($game6);
        $manager->persist($game7);
        $manager->persist($game8);
        $manager->persist($game9);
        $manager->persist($game10);
        $manager->persist($game11);
        $manager->persist($game12);
        $manager->persist($game13);
        $manager->persist($game14);
        $manager->persist($game15);
        $manager->persist($game16);
        $manager->persist($game17);
        $manager->persist($game18);
        $manager->persist($game19);
        $manager->persist($game20);

        // Créer les plateformes
        $plateformNames = ['PS5', 'Xbox Series X', 'Nintendo Switch', 'PC', 'PlayStation 4', 'Xbox One'];
        foreach ($plateformNames as $plateformName) {
            $plateforme = new Plateforme();
            $plateforme->setNamePlateforme($plateformName);
            $plateforme->setCreatedAt($created);
            $plateforme->setUpdatedAt($updated);
            $plateforme->setStatus("on");
            $plateformesEntries[] = $plateforme;
            $manager->persist($plateforme);
        }

        // Créer les avis pour chaque jeu
        foreach ($gamesEntries as $gameEntry) {
            // Créer 4 avis pour chaque jeu
            for ($i = 0; $i < 4; $i++) {
                $avis = new Notice();
                $avis->setComment($this->faker->sentence(3));
                $avis->setNote($this->faker->numberBetween(1, 5)); // Note aléatoire entre 1 et 5
                $avis->setUser($users[array_rand($users, 1)]);
                $avis->setGame($gameEntry);
                $avis->setCreateAt($created);
                $avis->setUpdateAt($updated);

                $noticesEntries[]= $avis;
                $manager->persist($avis);
            }
        }

        //Créer des catégories
        $categoryNames = ['Action/Aventure', 'FPS', 'RPG', 'Simulation', 'MMO', 'Courses/Sports', 'De Plateforme', 'Combat', 'Gestion/Stratégie'];
        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription($this->faker->sentence(10));
            $category->setStatus('on');
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());
            $categoriesEntries[] = $category; 
            $manager->persist($category);
        }

        // Associer les plateformes et les avis à chaque jeu
        foreach ($gamesEntries as $gameEntry) {
            $plateforme = $plateformesEntries[array_rand($plateformesEntries)];
            $avis = $noticesEntries[array_rand($noticesEntries)];
            $categ = $categoriesEntries[array_rand($categoriesEntries)];
            $gameEntry->addPlateforme($plateforme);
            $gameEntry->addNotice($avis);
            $gameEntry->addCategId($categ);
            $manager->persist($gameEntry);
        }

    // Flush des données
    $manager->flush();    
    }
}
