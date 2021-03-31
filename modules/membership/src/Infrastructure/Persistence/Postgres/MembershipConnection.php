<?php

declare(strict_types=1);

namespace Collector\Membership\Infrastructure\Persistence\Postgres;

use Doctrine\DBAL\Connection;

final class MembershipConnection extends Connection
{
    // Wrapper for type hinting our specific db connection for membership db.
}