<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\Request;

use DomainException;

final class NoRequestException extends DomainException
{
}
