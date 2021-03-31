<?php

declare(strict_types=1);

namespace Collector\Membership\Transport\Http\Member\Request;

use Collector\SharedKernel\Transport\Http\Request\Field;
use Collector\SharedKernel\Transport\Http\Request\ValidatableRequest;
use Collector\SharedKernel\Transport\Http\Request\ValidationConstraints;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

final class CreateRequest extends ValidatableRequest
{
    private const EMAIL_KEY = 'email';
    private const PASSWORD_KEY = 'password';

    private const MIN_PASSWORD_LENGTH = 8;
    private const MAX_PASSWORD_LENGTH = 72;

    public function email(): string
    {
        return $this->request->request->get(CreateRequest::EMAIL_KEY);
    }

    public function password(): string
    {
        return $this->request->request->get(CreateRequest::PASSWORD_KEY);
    }

    protected function constraints(): ValidationConstraints
    {
        return new ValidationConstraints(
            new Field(CreateRequest::EMAIL_KEY, new Required(new Email())),
            new Field(CreateRequest::PASSWORD_KEY, new Required([
                new Type('string'),
                new Length(min: CreateRequest::MIN_PASSWORD_LENGTH, max: CreateRequest::MAX_PASSWORD_LENGTH),
            ])),
        );
    }
}