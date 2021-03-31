<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\ValueObject;

use Stringable;
use Webmozart\Assert\Assert;

final class HashedPassword implements Stringable
{
    private string $password;

    public function __construct(string $password)
    {
        Assert::true(
            password_get_info($password)['algo'] === PASSWORD_BCRYPT,
            'password was not hashed using the bcrypt algorithm'
        );

        $this->password = $password;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->password;
    }

    public function matches(string $rawPassword): bool
    {
        return password_verify($rawPassword, $this->password);
    }
}
