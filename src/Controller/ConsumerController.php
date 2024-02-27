<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/partners/{partner_id}/consumers/{id}', name: 'api_detailConsumer', methods: ['GET'])]
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

    #[Route('/api/partners/{partner_id}/consumers/{id}', name: 'api_deleteConsumer', methods: ['DELETE'])]
    public function deleteConsumer(
        int $partner_id,
        Consumer $consumer,
        EntityManagerInterface $em,
    ): JsonResponse {
        if ($consumer->getPartner()->getId() !== $partner_id) {
            return new JsonResponse(['error' => 'Le consommateur n\'appartient pas à votre portefeuille client'], Response::HTTP_BAD_REQUEST);
        }
        $em->remove($consumer);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/partners/{partner_id}/consumers', name: 'api_createConsumer', methods: ['POST'])]
    public function createConsumer(
        int $partner_id,
        PartnerRepository $partnerRepository,
        Request $request,
        SerializerInterface $serializerInterface,
        EntityManagerInterface $em,
    ): JsonResponse {
        $partner = $partnerRepository->find($partner_id);
        if (!$partner) {
            return new JsonResponse(["error" => "Partner not found"], Response::HTTP_NOT_FOUND);
        }
    
        $consumer = $serializerInterface->deserialize($request->getContent(), consumer::class, 'json');
        $consumer->setPartner($partner);
        $em->persist($consumer);
        $em->flush();

        $jsonConsumer = $serializerInterface->serialize($consumer, 'json', ['groups' => 'getconsumers']);
        
        return new JsonResponse($jsonConsumer, Response::HTTP_CREATED, [], true);
    }

}
