<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Infrastructure\MessageBus;

use Collector\SharedKernel\Infrastructure\MessageBus\Event;
use Collector\SharedKernel\Infrastructure\MessageBus\EventBus;
use Collector\SharedKernel\Infrastructure\MessageBus\SymfonyMessengerEventBus;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SymfonyMessengerEventBusTest extends TestCase
{
    private MessageBusInterface $messageBus;
    private SymfonyMessengerEventBus $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->sut = new SymfonyMessengerEventBus($this->messageBus);
    }

    public function testCanBeUsedAsAnEventBus(): void
    {
        $this->assertInstanceOf(EventBus::class, $this->sut);
    }

    public function testTheEventIsPublishedOnTheBus(): void
    {
        $event = $this->fakeEvent();

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event, [new HandledStamp(null, 'test')]));

        $this->sut->publish($event);
    }

    public function testTheEventsArePublishedOnTheBus(): void
    {
        $eventA = $this->fakeEvent();
        $eventB = $this->fakeEvent();

        $this->messageBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive([$eventA], [$eventB])
            ->willReturnOnConsecutiveCalls(
                new Envelope($eventA, [new HandledStamp(null, 'test')]),
                new Envelope($eventB, [new HandledStamp(null, 'test')]),
            );

        $this->sut->publish($eventA, $eventB);
    }

    public function testNestedExceptionIsExtractedWhenHandlerFails(): void
    {
        $event = $this->fakeEvent();

        $this->messageBus->method('dispatch')->willThrowException(
            new HandlerFailedException(
                new Envelope($event, [new HandledStamp(null, 'test')]),
                [new DomainException('this is a nested exception')],
            ),
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('this is a nested exception');

        $this->sut->publish($event);
    }

    private function fakeEvent(): Event
    {
        return new class () implements Event {
            public function name(): string
            {
                return 'TestEvent';
            }
        };
    }
}
