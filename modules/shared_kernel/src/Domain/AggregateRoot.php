<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Domain;

abstract class AggregateRoot
{
    private DomainEvents $domainEvents;

    final public function recordedEvents(): DomainEvents
    {
        $this->lazyInit();

        return $this->domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->lazyInit();

        $this->domainEvents->push($domainEvent);
    }

    private function lazyInit(): void
    {
        if (isset($this->domainEvents)) {
            return;
        }

        $this->domainEvents = new DomainEvents();
    }
}
