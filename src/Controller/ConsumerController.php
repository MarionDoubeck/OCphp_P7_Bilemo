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

class ConsumerController extends AbstractController
{
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

        $idCache = "getAllConsumers-".$page."-".$limit;

        $consumerList = $cache->get($idCache, function (ItemInterface $itemInCache) use ($consumerRepository, $partner_id, $page, $limit) {
            echo ('pas encore en cache');
            $itemInCache->tag("consumersCache");
            return $consumerRepository->findAllByPartnerIdWithPagination($partner_id, $page, $limit);
        });

        $context = SerializationContext::create()->setGroups(['getPartner']);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

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
