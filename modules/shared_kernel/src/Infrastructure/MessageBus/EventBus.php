<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

interface EventBus
{
    /**
     * Publish Events on the message bus.
     */
    public function publish(Event ...$events): void;
}
