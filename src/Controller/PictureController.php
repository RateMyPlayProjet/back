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
    /**
     * Récupère toutes les images.
     *
     * @param PictureRepository $repository Le repository pour récupérer les images depuis la base de données.
     * @param SerializerInterface $serializer Le service de sérialisation pour la sérialisation.
     * @return JsonResponse La réponse JSON contenant la liste sérialisée des images, ou une réponse de non trouvé si aucune image n'est trouvée.
     */
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
    /**
     * Récupère l'image associée à un jeu par son identifiant.
     *
     * @param int $id L'identifiant du jeu.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour interagir avec la base de données.
     * @return Response La réponse HTTP contenant le contenu de l'image ou une exception si aucune image n'est trouvée pour le jeu spécifié.
    */
    #[Route('/api/images/game/{id}', name: 'picture.getGameById', methods: ['GET'])]
    public function getImageByGameId(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le jeu à partir de l'ID
        $game = $entityManager->getRepository(Game::class)->find($id);

        // Vérifier si le jeu existe
        if (!$game) {
            throw $this->createNotFoundException('Game not found');
        }

        // Récupérer les images associées au jeu
        $images = $game->getPictures();

        // Vérifier si des images ont été trouvées
        if (!$images) {
            throw $this->createNotFoundException('Images not found for the game ID: ' . $id);
        }

        // Pour l'exemple, supposons que vous souhaitez renvoyer uniquement la première image trouvée
        $image = $images[0];

        // Récupérer le contenu de l'image
        $imageData = file_get_contents('/var/www/html/symfony/projetFullStack/public/medias/pictures/' . $image->getRealPath());

        // Créer une réponse avec le contenu de l'image
        $response = new Response($imageData);

        // Définir les en-têtes de réponse appropriés
        $response->headers->set('Content-Type', $image->getMimeType());

        return $response;
    }

    /**
     * Récupère l'image associée à un utilisateur par son identifiant.
     *
     * @param int $id L'identifiant de l'utilisateur.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour interagir avec la base de données.
     * @return Response La réponse HTTP contenant le contenu de l'image ou une exception si aucune image n'est trouvée pour l'utilisateur spécifié.
    */
    #[Route('/api/images/user/{id}', name: 'picture.getUserById', methods: ['GET'])]
    public function getImageByUserId(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le jeu à partir de l'ID
        $user = $entityManager->getRepository(User::class)->find($id);

        // Vérifier si le jeu existe
        if (!$user) {
            throw $this->createNotFoundException('Game not found');
        }

        // Récupérer les images associées au jeu
        $images = $user->getPicture();

        // Vérifier si des images ont été trouvées
        if (!$images) {
            throw $this->createNotFoundException('Images not found for the game ID: ' . $id);
        }

        // Pour l'exemple, supposons que vous souhaitez renvoyer uniquement la première image trouvée
        $image = $images;

        // Récupérer le contenu de l'image
        $imageData = file_get_contents('/var/www/html/symfony/projetFullStack/public/medias/pictures/' . $image->getRealPath());

        // Créer une réponse avec le contenu de l'image
        $response = new Response($imageData);

        // Définir les en-têtes de réponse appropriés
        $response->headers->set('Content-Type', $image->getMimeType());

        return $response;
    }


    /**
     * Récupère l'image
     *
     * @param int $id L'identifiant de l'image.
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour interagir avec la base de données.
     * @return Response La réponse HTTP contenant le contenu de l'image ou une exception si aucune image n'est trouvée pour l'utilisateur spécifié.
    */
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

    /**
     * Crée une nouvelle image à partir des données fournies dans la requête.
     *
     * @param Request $request L'objet de requête HTTP contenant les données de l'image à créer.
     * @param EntityManagerInterface $entityManager L'interface pour interagir avec l'entité Picture dans la base de données.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données de l'image en JSON.
     * @param UrlGeneratorInterface $urlGenerator L'interface pour générer des URL absolues.
     * @return JsonResponse Une réponse JSON contenant les détails de l'image créée.
    */
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

    /**
     * Met à jour une image associée à un jeu spécifié.
     *
     * @param Picture $picture L'objet Picture à mettre à jour.
     * @param Request $request L'objet de requête HTTP contenant les données de mise à jour de l'image.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données de l'image en JSON.
     * @param EntityManagerInterface $entityManager L'interface pour interagir avec l'entité Picture dans la base de données.
     * @param TagAwareCacheInterface $cache L'interface pour gérer le cache et invalider les données en cache si nécessaire.
     * @return JsonResponse Une réponse JSON indiquant le succès de la mise à jour de l'image.
     */
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


    /**
     * Met à jour une image associée à un utilisateur spécifié.
     *
     * @param Picture $picture L'objet Picture à mettre à jour.
     * @param Request $request L'objet de requête HTTP contenant les données de mise à jour de l'image.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données de l'image en JSON.
     * @param EntityManagerInterface $entityManager L'interface pour interagir avec l'entité Picture dans la base de données.
     * @param TagAwareCacheInterface $cache L'interface pour gérer le cache et invalider les données en cache si nécessaire.
     * @return JsonResponse Une réponse JSON indiquant le succès de la mise à jour de l'image.
    */

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
