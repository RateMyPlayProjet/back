<?php

namespace App\Controller;

use App\Entity\Films;
use App\Repository\FilmsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Serializable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class FilmController extends AbstractController
{
    #[Route('/api/film', name: 'film.getAll')]
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
    /* public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FilmController.php',
        ]);
    } */
}
