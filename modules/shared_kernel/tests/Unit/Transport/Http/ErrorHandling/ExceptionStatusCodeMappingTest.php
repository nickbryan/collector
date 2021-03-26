<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Transport\Http\ErrorHandling;

use Collector\SharedKernel\Transport\Http\ErrorHandling\ExceptionStatusCodeMapping;
use Collector\SharedKernel\Transport\Http\Request\ValidationFailedException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ExceptionStatusCodeMappingTest extends TestCase
{
    public function testStoresStatusCodeMappingForException(): void
    {
        $sut = new ExceptionStatusCodeMapping();

        $sut->register(DomainException::class, Response::HTTP_BAD_REQUEST);

        $this->assertTrue($sut->hasMappingsFor(DomainException::class));
    }

    public function testStatusCodeIsReturnedForMapping(): void
    {
        $sut = new ExceptionStatusCodeMapping();

        $sut->register(DomainException::class, Response::HTTP_BAD_REQUEST);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sut->statusCodeFor(DomainException::class));
    }

    public function testMappingIsOverriddenWhenRegisteredMoreThanOnce(): void
    {
        $sut = new ExceptionStatusCodeMapping();

        $sut->register(DomainException::class, Response::HTTP_BAD_REQUEST);
        $sut->register(DomainException::class, Response::HTTP_I_AM_A_TEAPOT);

        $this->assertEquals(Response::HTTP_I_AM_A_TEAPOT, $sut->statusCodeFor(DomainException::class));
    }

    public function testADefaultCodeOfInternalServiceErrorIsReturnedWhenNotRegistered(): void
    {
        $sut = new ExceptionStatusCodeMapping();

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $sut->statusCodeFor(DomainException::class));
    }

    public function testHasADefaultMappingForValidationFailedException(): void
    {
        $sut = new ExceptionStatusCodeMapping();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $sut->statusCodeFor(ValidationFailedException::class));
    }
}
