<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Domain;

use Countable;
use Iterator;
use OutOfBoundsException;

/**
 * Iterating over this collection consumes the encapsulated DomainEvents.
 *
 * @implements Iterator<int, DomainEvent>
 */
final class DomainEvents implements Iterator, Countable
{
    /** @var DomainEvent[] */
    private array $domainEvents;

    public function __construct(DomainEvent ...$domainEvents)
    {
        $this->domainEvents = $domainEvents;
    }

    public function push(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    public function current(): DomainEvent
    {
        $event =  array_shift($this->domainEvents);

        if ($event === null) {
            throw new OutOfBoundsException('no events left in the queue');
        }

        return $event;
    }

    public function next(): int
    {
        return 0;
    }

    public function key(): int
    {
        return 0;
    }

    public function valid(): bool
    {
        return count($this) > 0;
    }

    public function rewind(): void
    {
        // Noop
    }

    public function count(): int
    {
        return count($this->domainEvents);
    }
}
