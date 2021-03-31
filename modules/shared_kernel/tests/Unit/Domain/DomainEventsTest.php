<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Domain;

use Collector\SharedKernel\Domain\DomainEvent;
use Collector\SharedKernel\Domain\DomainEvents;
use PHPUnit\Framework\TestCase;

final class DomainEventsTest extends TestCase
{
    public function testIsCountable(): void
    {
        $events = new DomainEvents($this->fakeDomainEvent(), $this->fakeDomainEvent());

        $this->assertCount(2, $events);
    }

    public function testIterationConsumesTheEventsInTheQueue(): void
    {
        $events = new DomainEvents($this->fakeDomainEvent(), $this->fakeDomainEvent());

        foreach ($events as $event) {
            $this->assertInstanceOf(DomainEvent::class, $event);
        }

        $this->assertCount(0, $events);
    }

    private function fakeDomainEvent(): DomainEvent
    {
        return new class () extends DomainEvent
        {
            // Fake
        };
    }
}
