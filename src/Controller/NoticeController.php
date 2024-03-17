<?php

namespace App\Controller;

use App\Repository\NoticeRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    
}
