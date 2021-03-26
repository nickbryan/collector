<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerEventBus implements EventBus
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function publish(Event ...$events): void
    {
        try {
            foreach ($events as $event) {
                $this->messageBus->dispatch($event);
            }
        } catch (HandlerFailedException $e) {
            $nested = current($e->getNestedExceptions());
            if ($nested === false) {
                throw $e;
            }
            throw $nested;
        }
    }
}
