<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member;

use Collector\Membership\Domain\Member\ValueObject\Email;
use Collector\Membership\Domain\Member\ValueObject\HashedPassword;
use Collector\Membership\Domain\Member\ValueObject\Password;
use Collector\SharedKernel\Domain\ValueObject\Identifier;
use Ramsey\Uuid\Uuid;

final class MemberFactory
{
    public function createFromPrimitives(string $email, string $password): Member
    {
        return Member::create(
            new Email($email),
            new Password($password),
        );
    }

    public function restoreFromPrimitives(string $identifier, string $email, string $hashedPassword): Member
    {
        return Member::restore(
            new Identifier(Uuid::fromString($identifier)),
            new Email($email),
            new HashedPassword($hashedPassword),
        );
    }
}
