<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\ErrorHandling;

use Collector\SharedKernel\Transport\Http\Request\ValidationFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ExceptionListener
{
    private const DEFAULT_TITLE = 'An application error occurred.';
    private const DEFAULT_TYPE = 'https://tools.ietf.org/html/rfc2616#section-10';
    private const HTTP_TITLE = 'An HTTP error occurred.';
    private const ERROR_FORMAT = 'json';

    public function __construct(
        private KernelInterface $kernel,
        private SerializerInterface $serializer,
        private ExceptionStatusCodeMapping $statusCodeMapping,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $this->statusCodeMapping->statusCodeFor($exception::class);

        if ($exception instanceof ValidationFailedException) {
            $event->setResponse(
                JsonResponse::fromJsonString(
                    $this->serializer->serialize($exception->constraints(), ExceptionListener::ERROR_FORMAT),
                    $statusCode,
                ),
            );

            return;
        }

        if ($exception instanceof HttpException) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'type' => ExceptionListener::DEFAULT_TYPE,
                        'title' => ExceptionListener::HTTP_TITLE,
                        'detail' => $exception->getMessage(),
                    ],
                    $exception->getStatusCode(),
                ),
            );

            return;
        }

        if ($this->kernel->isDebug()) {
            throw $exception;
        }

        $event->setResponse(
            new JsonResponse(
                [
                    'type' => ExceptionListener::DEFAULT_TYPE,
                    'title' => ExceptionListener::DEFAULT_TITLE,
                    'detail' => $exception->getMessage(),
                ],
                $statusCode,
            ),
        );
    }
}
