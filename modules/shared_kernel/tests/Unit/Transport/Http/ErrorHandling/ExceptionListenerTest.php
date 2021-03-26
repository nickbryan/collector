<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Transport\Http\ErrorHandling;

use Collector\SharedKernel\Transport\Http\ErrorHandling\ExceptionListener;
use Collector\SharedKernel\Transport\Http\ErrorHandling\ExceptionStatusCodeMapping;
use Collector\SharedKernel\Transport\Http\Request\ValidationFailedException;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

final class ExceptionListenerTest extends TestCase
{
    private ExceptionStatusCodeMapping $statusCodeMapping;
    private KernelInterface $kernel;
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statusCodeMapping = new ExceptionStatusCodeMapping();
        $this->kernel = $this->createMock(KernelInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testHandlesValidationExceptions(): void
    {
        $this->serializer->method('serialize')->willReturn('some response body');
        $event = $this->invokeListener(new ValidationFailedException(new ConstraintViolationList()));

        $this->assertEquals('some response body', $event->getResponse()->getContent());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $event->getResponse()->getStatusCode());
    }

    public function testHandlesHttpExceptions(): void
    {
        $event = $this->invokeListener(new HttpException(Response::HTTP_GONE, 'gone mate'));

        $this->assertEquals([
            'title' => 'An HTTP error occurred.',
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'detail' => 'gone mate',
        ], $this->decodeJson((string) $event->getResponse()->getContent()));

        $this->assertEquals(Response::HTTP_GONE, $event->getResponse()->getStatusCode());
    }

    public function testThrowsTheExceptionIfInDebugMode(): void
    {
        $this->kernel->method('isDebug')->willReturn(true);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('my domain exception');

        $this->invokeListener(new DomainException('my domain exception'));
    }

    public function testUsesDefaultStatusCodeFromMappingWhenNoMappingRegistered(): void
    {
        $event = $this->invokeListener(new DomainException('my domain exception'));

        $this->assertEquals([
            'title' => 'An application error occurred.',
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'detail' => 'my domain exception',
        ], $this->decodeJson((string) $event->getResponse()->getContent()));

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
    }

    public function testUsesStatusCodeFromMappingWhenAMappingIsRegistered(): void
    {
        $this->statusCodeMapping->register(DomainException::class, Response::HTTP_I_AM_A_TEAPOT);

        $event = $this->invokeListener(new DomainException('my domain exception'));

        $this->assertEquals([
            'title' => 'An application error occurred.',
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'detail' => 'my domain exception',
        ], $this->decodeJson((string) $event->getResponse()->getContent()));

        $this->assertEquals(Response::HTTP_I_AM_A_TEAPOT, $event->getResponse()->getStatusCode());
    }

    private function invokeListener(Throwable $throwable): ExceptionEvent
    {
        $event = new ExceptionEvent($this->kernel, new Request(), KernelInterface::MASTER_REQUEST, $throwable);

        call_user_func(new ExceptionListener($this->kernel, $this->serializer, $this->statusCodeMapping), $event);

        return $event;
    }

    /**
     * @return mixed[]
     */
    private function decodeJson(string $json): array
    {
        return json_decode(
            json: $json,
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
