<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products', methods:['GET'])]
    public function getAllProducts(
        ProductRepository $productRepository,
        SerializerInterface $serializerInterface,
        Request $request
    ): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $productList = $productRepository->findAllWithPagination($page, $limit);
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
        } else {
            return new JsonResponse(['message' => 'Ce produit n\'est pas ou plus référencé.'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

}
