<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Domain;

use Collector\SharedKernel\Domain\AggregateRoot;
use Collector\SharedKernel\Domain\DomainEvent;
use PHPUnit\Framework\TestCase;

final class AggregateRootTest extends TestCase
{
    public function testEventsAreRecorded(): void
    {
        $aggregate = new class () extends AggregateRoot {
            public function testEvent(DomainEvent $event): void
            {
                $this->record($event);
            }
        };

        $event = new class () extends DomainEvent {
            // Fake
        };

        $this->assertCount(0, $aggregate->recordedEvents());

        $aggregate->testEvent(clone $event);
        $aggregate->testEvent(clone $event);
        $aggregate->testEvent(clone $event);

        $this->assertCount(3, $aggregate->recordedEvents());
    }
}
