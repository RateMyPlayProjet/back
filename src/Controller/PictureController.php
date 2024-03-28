<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    #[Route('/api/picture', name:"picture.getAll", methods:['GET'])]
    public function getAllPictures(PictureRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $pictures = $repository->findAll();

        if (empty($pictures)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        // Exclure les propriétés causant la référence circulaire lors de la sérialisation
        $context = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['game', 'notices', 'user']
        ];

        // Serialize the pictures into JSON
        $serializedPictures = $serializer->serialize($pictures, 'json', $context);

        return new JsonResponse($serializedPictures, Response::HTTP_OK, [], true);
    }

    #[Route('/api/images/game/{id}', name: 'get_images_by_game_id', methods: ['GET'])]
    public function getImageByGameId(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID de l'image correspondant au jeu
        $imageId = $request->query->get('image_id');

        // Vérifier si l'ID de l'image est fourni
        if (!$imageId) {
            throw $this->createNotFoundException('Image ID not provided');
        }

        // Récupérer l'image à partir de la base de données en fonction de son ID
        $image = $entityManager->getRepository(Picture::class)->find($imageId);

        // Récupérer le contenu de l'image
        $imageData = file_get_contents('/var/www/html/symfony/projetFullStack/public/medias/pictures/' . $image->getRealPath());

        // Créer une réponse avec le contenu de l'image
        $response = new Response($imageData);

        // Définir les en-têtes de réponse appropriés
        $response->headers->set('Content-Type', $image->getMimeType());

        return $response;
    }


    #[Route('/api/picture/{idPicture}', name:"picture.get", methods:['GET'])]
    public function getPicture(PictureRepository $repository, int $idPicture, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer):JsonResponse{
        $picture = $repository->find($idPicture);

        if(!$picture) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        // Exclure les propriétés causant la référence circulaire lors de la sérialisation
        $context = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['game', 'notices', 'user']
        ];

        $location = $urlGenerator->generate('app_picture',[], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace('/public/', "", $picture->getPublicPath())."/".$picture->getRealPath();

        return new JsonResponse($serializer->serialize($picture,'json', $context), Response::HTTP_OK, ["Location" => $location],true);
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

    #[Route('/api/picture/game/{id}', name: 'picture.update', methods: ['PUT'])]
    public function updatePictureByIdForGame(Picture $picture, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

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

    #[Route('/api/picture/user/{id}', name: 'picture.update', methods: ['PUT'])]
    public function updatePictureByIdForUser(Picture $picture, Request $request,  SerializerInterface $serializer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse{

        // Récupérer l'identifiant du jeu à partir du corps de la requête
        $data = json_decode($request->getContent(), true);
        $userId = $data['user_id'];

        // Récupérer le jeu correspondant à l'identifiant
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Vérifier si le jeu existe
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Mettre à jour l'image avec le jeu associé
        $updatedPicture = $serializer->deserialize($request->getContent(), Picture::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $picture
        ]);
        $updatedPicture->setUpdateAt(new \DateTime());
        $updatedPicture->setUser($user);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Invalider le cache
        $cache->invalidateTags(["pictureCache"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }
    
}
