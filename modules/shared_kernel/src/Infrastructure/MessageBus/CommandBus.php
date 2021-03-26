<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

interface CommandBus
{
    /**
     * Execute a Command through the message bus.
     */
    public function execute(Command $command): void;
}
