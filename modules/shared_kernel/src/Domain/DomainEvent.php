<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Domain;

use Collector\SharedKernel\Infrastructure\MessageBus\Event;

abstract class DomainEvent implements Event
{
    public function name(): string
    {
        return static::class;
    }
}
