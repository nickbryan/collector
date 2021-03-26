<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class ValidatableRequest
{
    protected Request $request;

    public function __construct(
        private ValidatorInterface $validator,
        RequestStack $requestStack,
    ) {
        $this->initialiseRequest($requestStack);
        $this->validate();
    }

    abstract protected function constraints(): ValidationConstraints;

    private function initialiseRequest(RequestStack $requestStack): void
    {
        $request = $requestStack->getCurrentRequest();

        if ($request === null) {
            throw new NoRequestException('there is no current request in the request stack');
        }

        $this->request = $request;
    }

    private function validate(): void
    {
        $violations = $this->validator->validate(
            $this->allRequestData(),
            $this->constraints()->constraints(),
        );

        if ($violations->count() > 0) {
            throw new ValidationFailedException($violations);
        }
    }

    /**
     * @return iterable<string, mixed>
     */
    private function allRequestData(): iterable
    {
        return $this->filteredRequestAttributes()
            + $this->request->request->all()
            + $this->request->query->all()
            + $this->request->files->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function filteredRequestAttributes(): array
    {
        return array_filter(
            $this->request->attributes->all(),
            static fn (string $key): bool => str_starts_with($key, '_') === false,
            ARRAY_FILTER_USE_KEY,
        );
    }
}