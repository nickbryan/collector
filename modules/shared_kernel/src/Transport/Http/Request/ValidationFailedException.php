<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\Request;

use RuntimeException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ValidationFailedException extends RuntimeException
{
    public function __construct(
        private ConstraintViolationListInterface $constraintViolationList,
    ) {
        parent::__construct('request validation failed');
    }

    public function constraints(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }
}