<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * Récupère toutes les catégories.
     *
     * @param CategoryRepository $repository Le repository des catégories.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données des catégories en JSON.
     * @param TagAwareCacheInterface $cache L'interface pour gérer le cache.
     * @return JsonResponse Une réponse JSON contenant toutes les catégories.
     */
    #[Route('/api/category', name: 'category.getAll', methods: ['GET'])]
    public function getAllCategories(CategoryRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse{
        
        $idCache = "getAllCategories";
        $cache->invalidateTags(["categoryCache"]);
        $categories = $repository->findAll();
        $jsonCateg= $serializer->serialize($categories,'json', ['groups'=> "getAllCategories"]);
        
        return new JsonResponse($jsonCateg,200,[],true);
    }

    /**
     * Récupère une catégorie par son identifiant.
     *
     * @param Category $category La catégorie à récupérer.
     * @param SerializerInterface $serializer L'interface pour sérialiser les données de la catégorie en JSON.
     * @return JsonResponse Une réponse JSON contenant les données de la catégorie.
     */
    #[Route('/api/category/{idCateg}', name: 'category.get', methods: ['GET'])]
    #[ParamConverter("category", options: ["id" => "idCateg"])]
    public function getCategory(Category $category, SerializerInterface $serializer): JsonResponse{
        $jsonGame= $serializer->serialize($category,'json', ['groups'=> "getAllCategories"]);
        return new JsonResponse($jsonGame,200,[],true);
    }
}
