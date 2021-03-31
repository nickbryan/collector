<?php

declare(strict_types=1);

namespace Collector\Membership\Domain\Member\Exception;

use DomainException;

final class MemberNotFoundException extends DomainException
{
    // When a member is not found in the repository.
}