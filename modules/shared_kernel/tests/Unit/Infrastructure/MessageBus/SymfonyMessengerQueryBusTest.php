<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Infrastructure\MessageBus;

use Collector\SharedKernel\Infrastructure\MessageBus\Query;
use Collector\SharedKernel\Infrastructure\MessageBus\QueryBus;
use Collector\SharedKernel\Infrastructure\MessageBus\Result;
use Collector\SharedKernel\Infrastructure\MessageBus\SymfonyMessengerQueryBus;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SymfonyMessengerQueryBusTest extends TestCase
{
    private MessageBusInterface $messageBus;
    private SymfonyMessengerQueryBus $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->sut = new SymfonyMessengerQueryBus($this->messageBus);
    }

    public function testCanBeUsedAsAQueryBus(): void
    {
        $this->assertInstanceOf(QueryBus::class, $this->sut);
    }

    public function testTheQueryIsDispatchedOnTheBus(): void
    {
        $query = $this->fakeQuery();

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn(new Envelope($query, [new HandledStamp($this->fakeResult(), 'test')]));

        $this->sut->query($query);
    }

    public function testNestedExceptionIsExtractedWhenHandlerFails(): void
    {
        $query = $this->fakeQuery();

        $this->messageBus->method('dispatch')->willThrowException(
            new HandlerFailedException(
                new Envelope($query, [new HandledStamp($this->fakeResult(), 'test')]),
                [new DomainException('this is a nested exception')],
            ),
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('this is a nested exception');

        $this->sut->query($query);
    }

    public function testValueIsReturnedFromQuery(): void
    {
        $query = $this->fakeQuery();
        $expectedResult = $this->fakeResult();

        $this->messageBus
            ->method('dispatch')
            ->willReturn(new Envelope($query, [new HandledStamp($expectedResult, 'test')]));

        $result = $this->sut->query($query);

        $this->assertEquals($expectedResult, $result);
    }

    private function fakeQuery(): Query
    {
        return new class () implements Query {
            // Fake
        };
    }

    private function fakeResult(): Result
    {
        return new class () implements Result {
            // Fake
        };
    }
}
