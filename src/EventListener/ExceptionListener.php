<?php
namespace App\EventListener;

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
        if ($exception instanceof UniqueConstraintViolationException) {
            $message = $exception->getPrevious()->getMessage();
            $response_message = 'Duplicate entry';
            $start = \strpos($message, $response_message);
            $end = \strpos($message, 'for key');
            if (\is_int($start) && \is_int($end)) {
                $start += \strlen($response_message) + 2;
                $field = \substr($message, $start, $end - $start - 2);
                $response = new JsonResponse(['error' => $response_message, 'field' => $field]);
                $response->setStatusCode(JsonResponse::HTTP_FORBIDDEN);
            } else {
                $response = new JsonResponse;
                $response->setContent($message);
            }
            $event->setResponse($response);
        }
    }
}