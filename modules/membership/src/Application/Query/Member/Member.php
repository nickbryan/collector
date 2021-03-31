<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Query\Member;

use Collector\SharedKernel\Infrastructure\MessageBus\Result;
use Ramsey\Uuid\UuidInterface;

final class Member implements Result
{
    public function __construct(private UuidInterface $identifier, private string $email) {}

    public function identifier(): UuidInterface
    {
        return $this->identifier;
    }

    public function email(): string
    {
        return $this->email;
    }
}