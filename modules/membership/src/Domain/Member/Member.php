<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member;

use Collector\Membership\Domain\Member\Event\MembershipCreatedEvent;
use Collector\Membership\Domain\Member\ValueObject\Email;
use Collector\Membership\Domain\Member\ValueObject\HashedPassword;
use Collector\Membership\Domain\Member\ValueObject\Password;
use Collector\SharedKernel\Domain\AggregateRoot;
use Collector\SharedKernel\Domain\ValueObject\Identifier;
use Ramsey\Uuid\Uuid;

final class Member extends AggregateRoot
{
    private function __construct(
        private Identifier $identifier,
        private Email $email,
        private HashedPassword $password,
    ) {}

    public static function create(Email $email, Password $password): Member
    {
        $member = new Member(new Identifier(Uuid::uuid4()), $email, $password->hashed());

        $member->record(new MembershipCreatedEvent(
            $member->identifier(),
            $member->email(),
        ));

        return $member;
    }

    public static function restore(Identifier $identifier, Email $email, HashedPassword $password): Member
    {
        return new Member($identifier, $email, $password);
    }

    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }
}