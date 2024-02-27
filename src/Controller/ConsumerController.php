<?php

namespace App\Controller;

use App\Repository\ConsumerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ConsumerController extends AbstractController
{
    #[Route('/api/partners/{partner_id}/consumers', name: 'api_consumers', methods:['GET'])]
    public function getAllconsumers(
        int $partner_id,
        ConsumerRepository $consumerRepository,
        SerializerInterface $serializerInterface
    ): JsonResponse
    {
        $consumerList = $consumerRepository->findByPartnerId($partner_id);
        $jsonConsumerList = $serializerInterface->serialize($consumerList, 'json', ['groups'=>'getPartner']);

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/partners/{partner_id}/consumers/{id}', name: 'api_detailconsumer', methods: ['GET'])]
    public function getDetailconsumer(
        int $partner_id,
        int $id,
        consumerRepository $consumerRepository,
        SerializerInterface $serializerInterface
    ): JsonResponse {

        $consumer = $consumerRepository->find($id);
        if ($consumer && $consumer->getPartner()->getId() === $partner_id) {
            $jsonConsumer = $serializerInterface->serialize($consumer, 'json', ['groups'=>'getPartner']);
            return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
        } else {
            return new JsonResponse(['message' => 'Ce client n\'existe pas ou n\'est pas associé à votre portefeuille client.'], Response::HTTP_NOT_FOUND);
        }
      
    }

}
