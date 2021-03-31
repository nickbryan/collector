<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Query\Member;

use Collector\SharedKernel\Infrastructure\MessageBus\Query;


final class MemberByEmailQuery implements Query
{
    public function __construct(private string $email) {}

    public function email(): string
    {
        return $this->email;
    }
}