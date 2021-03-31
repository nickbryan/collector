<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\ValueObject;

use Stringable;
use Webmozart\Assert\Assert;

final class Email implements Stringable
{
    private string $email;

    public function __construct(string $email)
    {
        Assert::email($email);

        $this->email = $email;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->email;
    }
}
