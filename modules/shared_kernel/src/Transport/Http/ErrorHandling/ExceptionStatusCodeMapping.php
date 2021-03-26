<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\ErrorHandling;

use Collector\SharedKernel\Transport\Http\Request\ValidationFailedException;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ExceptionStatusCodeMapping
{
    private const DEFAULT_STATUS_CODE = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

    private const DEFAULT_EXCEPTION_MAPPINGS = [
        ValidationFailedException::class => JsonResponse::HTTP_BAD_REQUEST,
    ];

    /** @var array<string, int> Where key is the exception type and value is the response code. */
    private array $exceptions = self::DEFAULT_EXCEPTION_MAPPINGS;

    public function register(string $exception, int $statusCode): void
    {
        $this->exceptions[$exception] = $statusCode;
    }

    public function hasMappingsFor(string $exception): bool
    {
        return isset($this->exceptions[$exception]);
    }

    public function statusCodeFor(string $exception): int
    {
        return $this->exceptions[$exception] ?? self::DEFAULT_STATUS_CODE;
    }
}
