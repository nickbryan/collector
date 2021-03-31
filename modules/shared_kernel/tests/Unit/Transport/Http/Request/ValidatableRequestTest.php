<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Transport\Http\Request;

use Collector\SharedKernel\Transport\Http\Request\Field;
use Collector\SharedKernel\Transport\Http\Request\NoRequestException;
use Collector\SharedKernel\Transport\Http\Request\ValidatableRequest;
use Collector\SharedKernel\Transport\Http\Request\ValidationConstraints;
use Collector\SharedKernel\Transport\Http\Request\ValidationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatableRequestTest extends TestCase
{
    private ValidatorInterface $validator;
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
    }

    public function testNoRequestExceptionIsThrownIfNoRequestInRequestStack(): void
    {
        $this->requestStack->method('getCurrentRequest')->willReturn(null);

        $this->expectException(NoRequestException::class);
        $this->expectExceptionMessage('there is no current request in the request stack');

        $this->mockRequest(new ValidationConstraints());
    }

    public function testValidationFailedExceptionIsThrownWhenAttributeValidationFails(): void
    {
        $attributes = ['some_key' => 'not_an_email'];
        $request = new Request(attributes: $attributes);
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($attributes, new Collection([
                'allowMissingFields' => true,
                'allowExtraFields' => false,
                'fields' => ['some_key' => new Required(new Email())],
            ]))
            ->willReturn(
                new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            );

        $this->expectException(ValidationFailedException::class);

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    public function testAttributeValidationIgnoresFieldsStartingWithUnderscore(): void
    {
        $attributes = ['_some_key' => 'not_an_email'];
        $request = new Request(attributes: $attributes);
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with([], new Collection([
               'allowMissingFields' => true,
               'allowExtraFields' => false,
               'fields' => ['some_key' => new Required(new Email())],
           ]))
            ->willReturn(
                new ConstraintViolationList([]),
            );

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    public function testValidationFailedExceptionIsThrownWhenQueryValidationFails(): void
    {
        $attributes = ['some_key' => 'not_an_email'];
        $request = new Request(query: $attributes);
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($attributes, new Collection([
                'allowMissingFields' => true,
                'allowExtraFields' => false,
                'fields' => ['some_key' => new Required(new Email())],
            ]))
            ->willReturn(
                new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            );

        $this->expectException(ValidationFailedException::class);

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    public function testValidationFailedExceptionIsThrownWhenRequestValidationFails(): void
    {
        $attributes = ['some_key' => 'not_an_email'];
        $request = new Request(request: $attributes);
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($attributes, new Collection([
                'allowMissingFields' => true,
                'allowExtraFields' => false,
                'fields' => ['some_key' => new Required(new Email())],
            ]))
            ->willReturn(
                new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            );

        $this->expectException(ValidationFailedException::class);

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    public function testValidationFailedExceptionIsThrownWhenFileValidationFails(): void
    {
        $attributes = ['some_key' => []];
        $request = new Request(files: $attributes);
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($attributes, new Collection([
                'allowMissingFields' => true,
                'allowExtraFields' => false,
                'fields' => ['some_key' => new Required(new Email())],
            ]))
            ->willReturn(
                new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            );

        $this->expectException(ValidationFailedException::class);

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    /**
     * @dataProvider priorityDataProvider
     *
     * @param mixed[] $expected
     */
    public function testRequestArgumentOrderOfPrecedence(Request $request, array $expected): void
    {
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($expected, new Collection([
                'allowMissingFields' => true,
                'allowExtraFields' => false,
                'fields' => ['some_key' => new Required(new Email())],
            ]))
            ->willReturn(
                new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]),
            );

        $this->expectException(ValidationFailedException::class);

        $this->mockRequest(new ValidationConstraints(fields: new Field('some_key', new Required(new Email()))));
    }

    /**
     * @return iterable<mixed[]>
     */
    public function priorityDataProvider(): iterable
    {
        yield [new Request(request: ['some_key' => 1], attributes: ['some_key' => 2]), ['some_key' => 2]];
        yield [new Request(query: ['some_key' => 1], attributes: ['some_key' => 2]), ['some_key' => 2]];
        yield [new Request(attributes: ['some_key' => 2], files: ['some_key' => []]), ['some_key' => 2]];
        yield [new Request(query: ['some_key' => 1], request: ['some_key' => 2]), ['some_key' => 2]];
        yield [new Request(request: ['some_key' => 2], files: ['some_key' => []]), ['some_key' => 2]];
        yield [new Request(query: ['some_key' => 2], files: ['some_key' => []]), ['some_key' => 2]];
    }

    private function mockRequest(ValidationConstraints $constraints): ValidatableRequest
    {
        return new class(
            $this->validator,
            $this->requestStack,
            $constraints,
        ) extends ValidatableRequest {
            public function __construct(
                ValidatorInterface $validator,
                RequestStack $requestStack,
                private ValidationConstraints $validationConstraints,
            ) {
                parent::__construct($validator, $requestStack);
            }

            protected function constraints(): ValidationConstraints
            {
                return $this->validationConstraints;
            }
        };
    }
}