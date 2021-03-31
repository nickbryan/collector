<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\ValueObject;

use Webmozart\Assert\Assert;

final class Password
{
    private const MIN_PASSWORD_LENGTH = 8;
    private const MAX_PASSWORD_LENGTH = 72;

    private string $password;

    public function __construct(string $password)
    {
        Assert::lengthBetween($password, Password::MIN_PASSWORD_LENGTH, Password::MAX_PASSWORD_LENGTH);

        $this->password = $password;
    }

    public function hashed(): HashedPassword
    {
        return new HashedPassword(password_hash($this->password, PASSWORD_BCRYPT));
    }
}
