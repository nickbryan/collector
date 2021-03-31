<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\Event;

use Collector\SharedKernel\Domain\DomainEvent;
use Stringable;

final class MembershipCreatedEvent extends DomainEvent
{
    public function __construct(private string|Stringable $identifier, private string|Stringable $email) {}

    public function identifier(): string
    {
        return (string) $this->identifier;
    }

    public function email(): string
    {
        return (string) $this->email;
    }
}
