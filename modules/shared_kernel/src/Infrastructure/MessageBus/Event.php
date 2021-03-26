<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

interface Event
{
    /**
     * The name of the event (usually the class name).
     */
    public function name(): string;
}
