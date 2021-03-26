<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerCommandBus implements CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function execute(Command $command): void
    {
        try {
            $this->handle($command);
        } catch (HandlerFailedException $e) {
            $nested = current($e->getNestedExceptions());
            if ($nested === false) {
                throw $e;
            }
            throw $nested;
        }
    }
}
