<?php

declare(strict_types=1);

namespace Collector\Membership\Application\Command\Member;

use RuntimeException;

final class MemberAlreadyExistsException extends RuntimeException
{
    // When a member already exists within the application.
}