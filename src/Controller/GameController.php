<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameController extends AbstractController
{
    #[Route('/api/game', name: 'game.getAll', methods: ['GET'])]
    public function getAllGame(GameRepository $repository, SerializerInterface $serializer): JsonResponse{
        $games = $repository->findAll();
        $jsonFilm= $serializer->serialize($games,'json', ['groups'=> "getAll"]);
        return new JsonResponse($jsonFilm,200,[],true);
        /* dd($films);//equivalent de console.log */
    }

    #[Route('/api/game/{idGame}', name: 'game.get', methods: ['GET'])]
    #[ParamConverter("game", options: ["id" => "idGame"])]
    
    public function getGame(Game $game, SerializerInterface $serializer): JsonResponse{
       /*  $repository->findByStatus("on", $idGame); */
        $jsonGame= $serializer->serialize($game,'json', ['groups'=> "getAll"]);
        return new JsonResponse($jsonGame,200,[],true);
        /* dd($films);//equivalent de console.log */
    }

    /**
     * Create new game
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/game', name: 'game.post', methods: ['POST'])]
    //#[ParamConverter("film", options: ["id" => "idFilm"])]
    public function createGame(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator,GameRepository $gameRepository, ValidatorInterface $validator): JsonResponse{

        $game = $serializer->deserialize($request->getContent(), Game::class,'json');
        $dateNow = new \DateTime();

        $plateforme = $request->toArray("plateformes");
        /* dd($plateformes); */
        $gameRepository->find($plateforme);
        if(!is_null($plateforme) && $plateforme instanceof Game){
            $game->addEvolution($plateforme);
        }

        $game
        ->setStatus("on")
        ->setCreateAt($dateNow)
        ->setUpdateAt($dateNow);
    
        $errors = $validator->validate($game);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        //$film = new Films();
        //$film->setName("Malgré moi")->setAuthor("C'est moi wsh")->setType("Horreur")->setDate("05/10/2023")->setStatus("on");
        $entityManager->persist($game);
        $entityManager->flush();

        $jsonFilm= $serializer->serialize($game,'json');

        $location = $urlGenerator->generate('film.get', ['idFilm'=> $game->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonFilm,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update Films with a id
     *
     * @param Game $films
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/film/{id}', name: 'film.update', methods: ['PUT'])]
    public function updateGame(Game $game, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse{

        $updatedFilm = $serializer->deserialize($request->getContent(), Game::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$game]);
        $updatedFilm->setUpdateAt(new \DateTime());
        $entityManager->persist($updatedFilm);
        $entityManager->flush();

        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }

    /** 
     * Update Films with a id
     *
     * @param Game $games
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/game/{id}', name: 'game.delete', methods: ['DELETE'])]
    public function softDeleteGame(Game $games, Request $request, EntityManagerInterface $entityManager): JsonResponse{
        
        $game = $request->toArray()["force"];
        if($game === true){
            $entityManager->remove($games);
            
        }else{
            $game->setUpdateAt(new \DateTime())
            ->setStatus("off");
            $entityManager->persist($game);
        }
        $entityManager->flush();
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }
    /* public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FilmController.php',
        ]);
    } */
}
