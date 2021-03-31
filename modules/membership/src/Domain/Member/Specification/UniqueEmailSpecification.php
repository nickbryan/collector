<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\Specification;

use Collector\Membership\Domain\Member\Exception\MemberNotFoundException;
use Collector\Membership\Domain\Member\Member;
use Collector\Membership\Domain\Member\MemberRepository;

final class UniqueEmailSpecification
{
    public function __construct(private MemberRepository $memberRepository) {}

    public function isSatisfiedBy(Member $member): bool
    {
        try {
            $this->memberRepository->whereEmailIs($member->email());
        } catch (MemberNotFoundException) {
            return true;
        }

        return false;
    }
}
