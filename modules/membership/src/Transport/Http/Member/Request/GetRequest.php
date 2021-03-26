<?php

declare(strict_types=1);

namespace Collector\Membership\Transport\Http\Member\Request;

use Collector\SharedKernel\Transport\Http\Request\Field;
use Collector\SharedKernel\Transport\Http\Request\ValidatableRequest;
use Collector\SharedKernel\Transport\Http\Request\ValidationConstraints;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Uuid;

final class GetRequest extends ValidatableRequest
{
    public function memberId(): UuidInterface
    {
        return UuidV4::fromString($this->request->attributes->get('member_id'));
    }

    protected function constraints(): ValidationConstraints
    {
        return new ValidationConstraints(fields:
            new Field('member_id', new Required(new Uuid(versions: [Uuid::V4_RANDOM]))),
        );
    }
}