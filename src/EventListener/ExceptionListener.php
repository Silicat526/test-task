<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', priority: 2)]
final class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {

        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $responseData = [
            'status' => 'error',
            'code' => $statusCode,
            'message' => $exception->getMessage(),
        ];

        $response = new JsonResponse($responseData, $statusCode);

        $event->setResponse($response);
    }
}
