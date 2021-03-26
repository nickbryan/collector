<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Infrastructure\MessageBus;

use Collector\SharedKernel\Infrastructure\MessageBus\Command;
use Collector\SharedKernel\Infrastructure\MessageBus\CommandBus;
use Collector\SharedKernel\Infrastructure\MessageBus\SymfonyMessengerCommandBus;
use DomainException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class SymfonyMessengerCommandBusTest extends TestCase
{
    private MessageBusInterface $messageBus;
    private SymfonyMessengerCommandBus $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->sut = new SymfonyMessengerCommandBus($this->messageBus);
    }

    public function testCanBeUsedAsACommandBus(): void
    {
        $this->assertInstanceOf(CommandBus::class, $this->sut);
    }

    public function testTheCommandIsDispatchedOnTheBus(): void
    {
        $command = $this->fakeCommand();

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command, [new HandledStamp(null, 'test')]));

        $this->sut->execute($command);
    }

    public function testNestedExceptionIsExtractedWhenHandlerFails(): void
    {
        $command = $this->fakeCommand();

        $this->messageBus->method('dispatch')->willThrowException(
            new HandlerFailedException(
                new Envelope($command, [new HandledStamp(null, 'test')]),
                [new DomainException('this is a nested exception')],
            ),
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('this is a nested exception');

        $this->sut->execute($command);
    }

    private function fakeCommand(): Command
    {
        return new class () implements Command {
            // Fake
        };
    }
}
