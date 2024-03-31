<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Notice;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class NoticeController extends AbstractController
{
    /**
     * Renvoie tous les avis
     *
     * @param NoticeRepository $repository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     */
    #[Route('/api/notice', name: 'notice.getAll', methods: ['GET'])]
    public function getAllNotices(NoticeRepository $repository,SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse{
        
        $idCache = "getAllNotice";
        $cache->invalidateTags(["noticeCache"]);
        $jsonGame= $cache->get($idCache, function(ItemInterface $item) use($repository, $serializer){
            
            $item->tag("noticeCache");
            $notices = $repository->findAll();
            return $serializer->serialize($notices,'json', ['groups'=> "getAllNotices"]);
        });
        
        return new JsonResponse($jsonGame,200,[],true);
    }

    #[Route('/api/notice/{idNotice}', name: 'notice.get', methods: ['GET'])]
    #[ParamConverter("notice", options: ["id" => "idNotice"])]
    
    public function getNotice(Notice $notice, SerializerInterface $serializer): JsonResponse{
        $jsonNotice= $serializer->serialize($notice,'json', ['groups'=> "getAllNotices"]);
        return new JsonResponse($jsonNotice,200,[],true);
    }


    #[Route('/api/user/{userId}/notices/new', name:'create_notice_for_user', methods: ['POST'])]
    public function createNoticeForUser(Request $request,SerializerInterface $serializer,TagAwareCacheInterface $cache,EntityManagerInterface $entityManager,UrlGeneratorInterface $urlGenerator,ValidatorInterface $validator,int $userId): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $userRepository = $entityManager->getRepository(User::class);

        // Récupérez l'utilisateur à partir de l'ID.
        $user = $userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $notice = $serializer->deserialize($request->getContent(), Notice::class, 'json');
        $dateNow = new \DateTime();
        
        $notice
            ->setUser($user)
            ->setCreateAt($dateNow)
            ->setUpdateAt($dateNow);

        // Ecrire game s'il est donné
        if (isset($data['gameId'])) {
            $game = $entityManager->getRepository(Game::class)->find($data['gameId']);
            if ($game) {
                $notice->setGame($game);
            } else {
                return new JsonResponse(['error' => 'Game not found'], JsonResponse::HTTP_NOT_FOUND);
            }
        }

        $errors = $validator->validate($notice);
        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        
        $entityManager->persist($notice);
        $entityManager->flush();
        
        $jsonNotice = $serializer->serialize($notice, 'json');
        
        $location = $urlGenerator->generate('notice.get', ['id' => $notice->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        return new JsonResponse($jsonNotice, Response::HTTP_CREATED, ["Location" => $location], true);
    }    
}
