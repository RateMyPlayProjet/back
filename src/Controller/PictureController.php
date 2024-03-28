<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    #[Route('/', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    #[Route('/api/picture/{idPicture}', name:"picture.get", methods:['GET'])]
    public function getPicture(PictureRepository $repository, int $idPicture, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer):JsonResponse{
        $picture = $repository->find($idPicture);

        $location = $urlGenerator->generate('app_picture',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace('/public/', "", $picture->getPublicPath())."/".$picture->getRealPath();

        return $picture ?
        new JsonResponse($serializer->serialize($picture,'json'), Response::HTTP_OK, ["Location" => $location],true) :
        new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    #[Route('/api/picture', name:'picture.create', methods: ['POST'])]
    public function createPicture(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator): JsonResponse {
        $picture = new Picture();
        $file = $request->files->get('file');
    
        $picture->setFile($file);
        $picture->setMimeType($file->getClientMimeType());
        $picture->setRealName($file->getClientOriginalName());
        $picture->setName($file->getClientOriginalName());
        $picture->setPublicPath('/public/medias/pictures');
        $picture->setStatus("on")
            ->setUpdateAt(new \DateTime())
            ->setCreateAt(new \DateTime());
    
        $entityManager->persist($picture);
        $entityManager->flush();
    
        $jsonResponse = $serializer->serialize($picture, "json");
        $location = $urlGenerator->generate('picture.get', ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
    
        return new JsonResponse($jsonResponse, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/picture/{id}', name: 'picture.update', methods: ['PUT'])]
    public function updatePicture(Picture $picture, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        // Récupérer l'identifiant du jeu à partir du corps de la requête
        $data = json_decode($request->getContent(), true);
        $gameId = $data['game_id'];

        // Récupérer le jeu correspondant à l'identifiant
        $game = $entityManager->getRepository(Game::class)->find($gameId);

        // Vérifier si le jeu existe
        if (!$game) {
            return new JsonResponse(['message' => 'Game not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Mettre à jour l'image avec le jeu associé
        $updatedPicture = $serializer->deserialize($request->getContent(), Picture::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $picture
        ]);
        $updatedPicture->setUpdateAt(new \DateTime());
        $updatedPicture->setGame($game);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Invalider le cache
        $cache->invalidateTags(["pictureCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }
    
}
