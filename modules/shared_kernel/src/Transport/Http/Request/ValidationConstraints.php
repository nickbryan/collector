<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Transport\Http\Request;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;

final class ValidationConstraints
{
    /** @var Field[] */
    private array $fields;

    public function __construct(
        private bool $allowMissingFields = false,
        private bool $allowExtraFields = false,
        Field ...$fields,
    ) {
        $this->fields = $fields;
    }

    public function constraints(): Collection
    {
        return new Collection([
            'allowMissingFields' => $this->allowMissingFields,
            'allowExtraFields' => $this->allowExtraFields,
            'fields' => $this->keyValueFields(),
        ]);
    }

    /**
     * @return array<string, Constraint>
     */
    private function keyValueFields(): array
    {
        return array_reduce(
            $this->fields,
            $this->keyValueReducer(),
            [],
        );
    }

    private function keyValueReducer(): callable
    {
        return static function (array $fields, Field $field): array {
            $fields[$field->name()] = $field->constraint();

            return $fields;
        };
    }
}