<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\Request;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Existence;

final class Field
{
    public function __construct(private string $name, private Existence $constraint) {}

    public function name(): string
    {
        return $this->name;
    }

    public function constraint(): Constraint
    {
        return $this->constraint;
    }
}