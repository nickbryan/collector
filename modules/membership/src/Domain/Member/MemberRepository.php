<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member;

use Collector\Membership\Domain\Member\Exception\MemberNotFoundException;
use Collector\Membership\Domain\Member\ValueObject\Email;

interface MemberRepository
{
    /**
     * Add a new member to the repository. The Email and Identifier must be unique.
     */
    public function add(Member $member): void;

    /**
     * Request an Account from the repository with the given Email.
     *
     * @throws MemberNotFoundException when no Member exists with the given Email.
     */
    public function whereEmailIs(Email $email): Member;
}