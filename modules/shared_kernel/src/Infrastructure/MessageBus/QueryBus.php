<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\MessageBus;

interface QueryBus
{
    /**
     * Execute a Query through the message bus getting the Result as an object.
     */
    public function query(Query $query): Result;
}
