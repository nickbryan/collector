<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\Serializer\Normalizer;

use LogicException;

final class DenormalizationNotSupportedException extends LogicException
{
}
