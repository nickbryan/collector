<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Query\Member;

use RuntimeException;

final class MemberNotFoundException extends RuntimeException
{
    // When a member was not found from the given query.
}