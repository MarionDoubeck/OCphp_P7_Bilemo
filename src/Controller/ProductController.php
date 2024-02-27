<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods:['GET'])]
    public function getAllProducts(
        ProductRepository $productRepository,
        SerializerInterface $serializerInterface
    ): JsonResponse
    {
        $productList = $productRepository->findAll();
        $jsonProductList = $serializerInterface->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }
}
