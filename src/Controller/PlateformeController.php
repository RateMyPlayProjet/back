<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Plateforme;
use Lcobucci\JWT\Validation\Validator;
use App\Repository\PlateformeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlateformeController extends AbstractController
{
    /**
     * Renvoie toutes les plateformes
     *
     * @param PlateformeRepository $repository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     */
    #[Route('/api/plateforme', name:'plateforme.getAll', methods: ['GET'])]
    public function getAllPlateforme(PlateformeRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse{
        $idCache = "getAllPlateforme";
        $cache->invalidateTags(["plateformeCache"]);
        $jsonPlateforme = $cache->get($idCache, function(ItemInterface $item)use($repository,$serializer){
            $item->tag("plateformeCache");
            $plateforme = $repository->findAll();
            return $serializer->serialize($plateforme,'json', ['groups'=>['getAll']]);
        });
        return new JsonResponse($jsonPlateforme, 200,[],true);
    }

    /**
     * Créer une nouvelle plateforme
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/plateforme/{idPlateforme}', name:'plateforme.post', methods: ['POST'])]
    public function createPlateforme(Request $request, SerializerInterface $serializer, TagAwareCacheInterface $cache, EntityManagerInterface $entityManager, UrlGenerator $urlGenerator, ValidatorInterface $validator): JsonResponse{
        $plateforme = $serializer->deserialize($request->getContent(), Plateforme::class,'json');
        $dateNow = new \DateTime();

        $plateformes = $request->toArray()['games'];
        if(!is_null($plateformes) && $plateformes instanceof Game) {
            $plateforme->addGame($plateformes);
        }
        
        $plateforme
        ->setStatus('on')
        ->setCreatedAt($dateNow)
        ->setUpdatedAt($dateNow);

        $errors = $validator->validate($plateformes);
        if($errors ->count() > 0){
            return new JsonResponse($serializer->serialize($errors,'json'),JsonResponse::HTTP_BAD_REQUEST,[],true);
        }
        
        $entityManager->persist($plateformes);
        $entityManager->flush();
        $cache->invalidateTags(["gameCache"]);

        $jsonGame= $serializer->serialize($plateformes,'json');

        $location = $urlGenerator->generate('game.get', ['idGame'=> $plateformes->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonGame,Response::HTTP_CREATED,["Location" => $location],true);
    }

    /** 
     * Update Plateforme with a id
     *
     * @param Plateforme $plateforme
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/plateforme/{idPlateforme}', name: 'plateforme.update', methods: ['PUT'])]
    public function updateGame(Plateforme $plateforme, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        $updatedPlateforme = $serializer->deserialize($request->getContent(), Plateforme::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$plateforme]);
        $updatedPlateforme->setUpdateAt(new \DateTime());
        $entityManager->persist($updatedPlateforme);
        $entityManager->flush();
        $cache->invalidateTags(["gameCache"]);
        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }
}
