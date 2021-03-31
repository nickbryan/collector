<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Command\Member\Create;

use Collector\SharedKernel\Infrastructure\MessageBus\Command;

final class CreateCommand implements Command
{
    public function __construct(
        private string $email,
        private string $password,
    ) {}

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}