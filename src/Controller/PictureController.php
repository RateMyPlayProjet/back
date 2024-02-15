<?php

namespace App\Controller;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    #[Route('/picture', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    #[Route('/api/picture', name:'picture.create', methods: ['POST'])]
    public function createPicture(Request $request, EntityManagerInterface $entityManager): JsonResponse{
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
        dd($file);
        return new JsonResponse;
    }
}
