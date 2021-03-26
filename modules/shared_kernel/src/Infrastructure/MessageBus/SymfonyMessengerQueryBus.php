<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class SymfonyMessengerQueryBus implements QueryBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function query(Query $query): Result
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $e) {
            $nested = current($e->getNestedExceptions());
            if ($nested === false) {
                throw $e;
            }
            throw $nested;
        }
    }
}
