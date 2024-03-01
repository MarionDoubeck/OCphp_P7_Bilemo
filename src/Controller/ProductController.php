<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{
    /**
     * Retrieves a paginated list of all products.
     *
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number to retrieve.",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="The maximum number of products per page.",
     *     @OA\Schema(type="int")
     * )
     * @OA\Response(
     *     response=200,
     *     description="List of products retrieved successfully",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Tag(name="Products")
     * 
     * @param ProductRepository $productRepository The product repository.
     * @param SerializerInterface $serializerInterface The serializer.
     * @param Request $request The request object.
     * @param TagAwareCacheInterface $cache The cache service.
     * @return JsonResponse The JSON response containing the paginated list of products.
     */
    #[Route('/api/products', name: 'api_products', methods:['GET'])]
    public function getAllProducts(
        ProductRepository $productRepository,
        SerializerInterface $serializerInterface,
        Request $request,
        TagAwareCacheInterface $cache
    ): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $idCache = "getAllProducts-".$page."-".$limit;

        $productList = $cache->get($idCache, function (ItemInterface $itemInCache) use ($productRepository, $page, $limit) {
            echo ('pas encore en cache');
            $itemInCache->tag("productsCache");
            return $productRepository->findAllWithPagination($page, $limit);
        });

        $jsonProductList = $serializerInterface->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }//end getAllProducts()


    /**
     * Retrieves details of a product.
     *
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Retrieve details of a product",
     *     description="Retrieves details of a product by its ID.",
     *     operationId="getDetailProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the product.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     *
     * @param int $id The ID of the product.
     * @param ProductRepository $productRepository The product repository.
     * @param SerializerInterface $serializerInterface The serializer.
     * @return JsonResponse The JSON response containing the product details.
     */
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
    }//end getDetailproduct()


}
