<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Subscribes to kernel.exception events and handles exceptions by returning JSON responses.
 */
class ExceptionSubscriber implements EventSubscriberInterface
{


    /**
     * Handles kernel.exception events.
     *
     * @param ExceptionEvent $event The event.
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException) {
            $data = [
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
        } else {
            $data = [
                'status' => 500, // Le status n'existe pas car ce n'est pas une exception HTTP, donc on met 500 par défaut.
                'message' => $exception->getMessage()
            ];

            $event->setResponse(new JsonResponse($data));
        }

    }//end onKernelException()


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];

    }//end getSubscribedEvents()


}
