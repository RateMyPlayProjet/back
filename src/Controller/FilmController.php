<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Films;
use App\Repository\FilmsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class FilmController extends AbstractController
{
    #[Route('/api/film', name: 'film.getAll', methods: ['GET'])]
    public function getAllFilm(FilmsRepository $repository, SerializerInterface $serializer): JsonResponse{
        $films = $repository->findAll();
        $jsonFilm= $serializer->serialize($films,'json');
        return new JsonResponse($jsonFilm,200,[],true);
        /* dd($films);//equivalent de console.log */
    }

    #[Route('/api/film/{idFilm}', name: 'film.get', methods: ['GET'])]
    #[ParamConverter("film", options: ["id" => "idFilm"])]
    
    public function getFilm(Films $film, SerializerInterface $serializer): JsonResponse{
        $jsonFilm= $serializer->serialize($film,'json');
        return new JsonResponse($jsonFilm,200,[],true);
        /* dd($films);//equivalent de console.log */
    }

    /**
     * Create new film
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/film', name: 'film.post', methods: ['POST'])]
    //#[ParamConverter("film", options: ["id" => "idFilm"])]
    public function createFilm(Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse{

        $film = $serializer->deserialize($request->getContent(), Films::class,'json');
        $dateNow = new \DateTime();
        
        $film
        ->setStatus("on")
        ->setCreateAt($dateNow)
        ->setUpdateAt($dateNow);
    
        //$film = new Films();
        //$film->setName("Malgré moi")->setAuthor("C'est moi wsh")->setType("Horreur")->setDate("05/10/2023")->setStatus("on");
        $entityManager->persist($film);
        $entityManager->flush();

        $jsonFilm= $serializer->serialize($film,'json');

        $location = $urlGenerator->generate('film.get', ['idFilm'=> $film->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonFilm,Response::HTTP_CREATED,["Location" => $location],true);

    }

    /** 
     * Update Films with a id
     *
     * @param Films $films
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/film/{id}', name: 'film.update', methods: ['PUT'])]
    public function updateFilm(Films $films, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse{

        $updatedFilm = $serializer->deserialize($request->getContent(), Films::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE =>$films]);
        $updatedFilm->setUpdateAt(new \DateTime());
        $entityManager->persist($updatedFilm);
        $entityManager->flush();

        return new JsonResponse(null,JsonResponse::HTTP_NO_CONTENT,[],false);

    }

    /** 
     * Update Films with a id
     *
     * @param Films $films
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    #[Route('/api/film/{id}', name: 'film.delete', methods: ['DELETE'])]
    public function softDeleteFilm(Films $films, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse{
        
        $film = $request->toArray()["force"];
        if($film === true){
            $entityManager->remove($films);
            
        }else{
            $films->setUpdateAt(new \DateTime())
            ->setStatus("off");
            $entityManager->persist($films);
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
