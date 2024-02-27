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

    #[Route('/api/products/{id}', name: 'api_detailProduct', methods: ['GET'])]
    public function getDetailproduct(
        int $id,
        ProductRepository $productRepository,
        SerializerInterface $serializerInterface
    ): JsonResponse {

        $product = $productRepository->find($id);
        if ($product) {
            $jsonProduct = $serializerInterface->serialize($product, 'json');
            return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
   }
   
}
