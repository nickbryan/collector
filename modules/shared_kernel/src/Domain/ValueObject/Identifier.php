<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Domain\ValueObject;

use Ramsey\Uuid\Rfc4122\Fields;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Stringable;
use Webmozart\Assert\Assert;

final class Identifier implements Stringable
{
    private UuidInterface $identifier;

    public function __construct(UuidInterface $identifier)
    {
        $fields = $identifier->getFields();
        Assert::isInstanceOf($fields, Fields::class);

        /** @var Fields $fields */
        Assert::eq($fields->getVersion(), Uuid::UUID_TYPE_RANDOM, 'identifier is not a v4 uuid');

        $this->identifier = $identifier;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function value(): string
    {
        return $this->identifier->toString();
    }
}
