<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ConsumerController extends AbstractController
{
    /**
     * Retrieves a paginated list of consumers associated with a specific partner.
     *
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Le numéro de la page à récupérer.",
     *     @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre maximal d'éléments par page.",
     *     @OA\Schema(type="int")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des consommateurs associés à un partenaire spécifique.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Consumer::class))
     *     )
     * )
     * @OA\Tag(name="Consumers")
     * 
     * @param int $partner_id The ID of the partner.
     * @param ConsumerRepository $consumerRepository The consumer repository.
     * @param SerializerInterface $serializer The serializer.
     * @param Request $request The request object.
     * @param TagAwareCacheInterface $cache The cache service.
     * @return JsonResponse The JSON response containing the paginated list of consumers.
     */
    #[Route('/api/partners/{partner_id}/consumers', name: 'api_consumers', methods:['GET'])]
    public function getAllconsumers(
        int $partner_id,
        ConsumerRepository $consumerRepository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cache
    ): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit',3);

        $cacheKey = sprintf('partner_%d_consumers_page_%d_limit_%d', $partner_id, $page, $limit);

        $consumerList = $cache->get($cacheKey, function (ItemInterface $itemInCache) use ($consumerRepository, $partner_id, $page, $limit) {
            $itemInCache->tag("consumersCache");
            $resultList = $consumerRepository->findAllByPartnerIdWithPagination($partner_id, $page, $limit);
            return $resultList;
        });

        if (count($consumerList) === 0) {
            return new JsonResponse(['message' => 'Ce partenaire n existe pas ou son portefeuille client est vide.'], Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(['getPartner']);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }


    /**
     * Retrieves details of a consumer associated with a specific partner.
     *
     * @OA\Get(
     *     path="/api/partners/{partner_id}/consumers/{id}",
     *     summary="Retrieve details of a consumer",
     *     description="Retrieves details of a consumer associated with a specific partner.",
     *     operationId="getDetailConsumer",
     *     tags={"Consumers"},
     *     @OA\Parameter(
     *         name="partner_id",
     *         in="path",
     *         description="The ID of the partner.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the consumer.",
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
     *         description="Consumer not found or not associated with the specified partner"
     *     )
     * )
     *
     * @param int $partner_id The ID of the partner.
     * @param int $id The ID of the consumer.
     * @param ConsumerRepository $consumerRepository The consumer repository.
     * @param SerializerInterface $serializer The serializer.
     * @return JsonResponse The JSON response containing the consumer details.
     */
    #[Route('/api/partners/{partner_id}/consumers/{id}', name: 'api_detailConsumer', methods: ['GET'])]
    public function getDetailconsumer(
        int $partner_id,
        int $id,
        consumerRepository $consumerRepository,
        SerializerInterface $serializer
    ): JsonResponse {

        $consumer = $consumerRepository->find($id);
        if ($consumer && $consumer->getPartner()->getId() === $partner_id) {
            $context = SerializationContext::create()->setGroups(['getPartner']);
            $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
            return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
        } else {
            return new JsonResponse(['message' => 'Ce client n\'existe pas ou n\'est pas associé à votre portefeuille client.'], Response::HTTP_NOT_FOUND);
        }
      
    }

    /**
     * Deletes a consumer associated with a specific partner.
     *
     * @OA\Delete(
     *     path="/api/partners/{partner_id}/consumers/{id}",
     *     summary="Delete a consumer",
     *     description="Deletes a consumer associated with a specific partner.",
     *     operationId="deleteConsumer",
     *     tags={"Consumers"},
     *     @OA\Parameter(
     *         name="partner_id",
     *         in="path",
     *         description="The ID of the partner.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the consumer to delete.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Consumer successfully deleted"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, consumer does not belong to the specified partner"
     *     )
     * )
     *
     * @param int $partner_id The ID of the partner.
     * @param Consumer $consumer The consumer entity to delete.
     * @param EntityManagerInterface $em The entity manager.
     * @param TagAwareCacheInterface $cache The cache service.
     * @return JsonResponse The JSON response indicating the success of the deletion.
     */
    #[Route('/api/partners/{partner_id}/consumers/{id}', name: 'api_deleteConsumer', methods: ['DELETE'])]
    public function deleteConsumer(
        int $partner_id,
        Consumer $consumer,
        EntityManagerInterface $em,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        if ($consumer->getPartner()->getId() !== $partner_id) {
            return new JsonResponse(['error' => 'Le consommateur n\'appartient pas à votre portefeuille client'], Response::HTTP_BAD_REQUEST);
        }
        $cache->invalidateTags(["consumersCache"]);
        $em->remove($consumer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Creates a new consumer associated with a specific partner.
     *
     * @OA\Post(
     *     path="/api/partners/{partner_id}/consumers",
     *     summary="Create a new consumer",
     *     description="Creates a new consumer associated with a specific partner.",
     *     operationId="createConsumer",
     *     tags={"Consumers"},
     *     @OA\Parameter(
     *         name="partner_id",
     *         in="path",
     *         description="The ID of the partner.",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Consumer data to create",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 ref="#/components/schemas/Consumer"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Consumer successfully created",
     *         @OA\JsonContent(ref="#/components/schemas/Consumer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, validation errors occurred"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Partner not found"
     *     )
     * )
     *
     * @param int $partner_id The ID of the partner.
     * @param PartnerRepository $partnerRepository The partner repository.
     * @param Request $request The request object.
     * @param SerializerInterface $serializer The serializer.
     * @param EntityManagerInterface $em The entity manager.
     * @param ValidatorInterface $validator The validator.
     * @param TagAwareCacheInterface $cache The cache service.
     * @return JsonResponse The JSON response containing the created consumer.
     */
    #[Route('/api/partners/{partner_id}/consumers', name: 'api_createConsumer', methods: ['POST'])]
    public function createConsumer(
        int $partner_id,
        PartnerRepository $partnerRepository,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cache
    ): JsonResponse {
        $partner = $partnerRepository->find($partner_id);
        if (!$partner) {
            return new JsonResponse(["error" => "Partner not found"], Response::HTTP_NOT_FOUND);
        }
    
        $consumer = $serializer->deserialize($request->getContent(), consumer::class, 'json');

        $errors = $validator->validate($consumer);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
            $consumer->setPartner($partner);
            $cache->invalidateTags(["consumersCache"]);
            $em->persist($consumer);
            $em->flush();
            $context = SerializationContext::create()->setGroups(['getPartner']);
            $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
            return new JsonResponse($jsonConsumer, Response::HTTP_CREATED, [], true);
    }

}
