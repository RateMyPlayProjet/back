<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/api/user/{username}', name: 'user.get', methods: ['GET'])]
    public function getUserId(UserRepository $repository, SerializerInterface $serializer, Security $security, string $username): JsonResponse {
        // Récupérer l'utilisateur actuellement authentifié
        $currentUser = $security->getUser();
        
        // Vérifier si l'utilisateur actuel est bien celui qui fait la demande
        if (!$currentUser || $currentUser->getUserIdentifier() !== $username) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Rechercher l'utilisateur dans la base de données en fonction de son nom d'utilisateur
        $user = $repository->findOneBy(['username' => $username]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Retourner l'ID de l'utilisateur dans la réponse JSON
        $jsonData = ['id' => $user->getId()];
        $jsonResponse = $serializer->serialize($jsonData, 'json');
        return new JsonResponse($jsonResponse, JsonResponse::HTTP_OK, [], true);
    }

}
