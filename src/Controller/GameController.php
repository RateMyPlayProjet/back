<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Picture;
use OpenApi\Attributes as OA;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class GameController extends AbstractController
{
    /**
     * Renvoie tous les jeux
     *
     * @param GameRepository $repository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     */
    #[OA\Response(
        response:200,
        description: "Retourne la liste des jeux",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(ref: new Model(type:Game::class))
        )
    )]
    #[Route('/api/game', name: 'game.getAll', methods: ['GET'])]
    public function getAllGames(GameRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse{
        
        $idCache = "getAllGames";
        $cache->invalidateTags(["gameCache"]);
        $jsonGame= $cache->get($idCache, function(ItemInterface $item) use($repository, $serializer){
            
            $item->tag("gameCache");
            $games = $repository->findAll();
            return $serializer->serialize($games,'json', ['groups'=> "getAllGames"]);
        });
        
        return new JsonResponse($jsonGame,200,[],true);
    }
    /**
     * Récupère les informations d'un jeu spécifié en utilisant son identifiant.
     *
     * @param Game $game L'objet Game à récupérer.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données du jeu en JSON.
     * @return JsonResponse Une réponse JSON contenant les informations du jeu demandé.
     */
    #[Route('/api/game/{idGame}', name: 'game.get', methods: ['GET'])]
    #[ParamConverter("game", options: ["id" => "idGame"])]
    
    public function getGame(Game $game, SerializerInterface $serializer): JsonResponse{
        $jsonGame= $serializer->serialize($game,'json', ['groups'=> "getAllGames"]);
        return new JsonResponse($jsonGame,200,[],true);
    }

    /**
     * Créer un nouveau jeu
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/game', name: 'game.post', methods: ['POST'])]
    public function createGame(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator, TagAwareCacheInterface $cache): JsonResponse{
        $game = $serializer->deserialize($request->getContent(), Game::class,'json');
        $dateNow = new \DateTime();       

        $plateforme = $request->toArray()["plateformes"];
        if(!is_null($plateforme) && $plateforme instanceof Game){
            $game->addEvolution($plateforme);
        }
        
        $game
        ->setStatus("on")
        ->setCreateAt($dateNow)
        ->setDateSortie($dateNow)
        ->setUpdateAt($dateNow);
    
        $errors = $validator->validate($game);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        
        $entityManager->persist($game);
        $entityManager->flush();
        $cache->invalidateTags(["gameCache"]);

        $jsonGame= $serializer->serialize($game,'json');

        $location = $urlGenerator->generate('game.get', ['idGame'=> $game->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGame,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update Game with a id
     *
     * @param Game $game
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/game/{id}', name: 'game.update', methods: ['PUT'])]
    public function updateGame(Game $game, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        $updatedGame = $serializer->deserialize($request->getContent(), Game::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$game]);
        $updatedGame->setUpdatedAt(new \DateTime()); // Utiliser setUpdatedAt au lieu de setUpdateAt
        $entityManager->persist($updatedGame);
        $entityManager->flush();
        $cache->invalidateTags(["gameCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }


    /** 
     * Delete Game with a id
     *
     * @param Game $games
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/game/{id}', name: 'game.delete', methods: ['DELETE'])]
    public function softDeleteGame(Game $games, Request $request, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{
        
        $game = $request->toArray()["force"];
        if($game === true){
            $entityManager->remove($games);
            
        }else{
            $game->setUpdateAt(new \DateTime())
            ->setStatus("off");
            $entityManager->persist($game);
        }
        $entityManager->flush();
        $cache->invalidateTags(["gameCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }
}
