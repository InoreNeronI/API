<?php
namespace App\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Listen only on the expected exception
        if ($exception instanceof ForeignKeyConstraintViolationException ||
            $exception instanceof UniqueConstraintViolationException) {
            $response = new JsonResponse(['error' => $exception->getPrevious()->getMessage()]);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $event->setResponse($response);
        }
    }
}
