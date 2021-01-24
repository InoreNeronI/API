<?php
namespace App\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // Listen only on the expected exception
        if ($exception instanceof ForeignKeyConstraintViolationException ||
            $exception instanceof UniqueConstraintViolationException) {
            $response = new JsonResponse(['error' => $exception->getPrevious()->getMessage()]);
            $response->setStatusCode(JsonResponse::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
