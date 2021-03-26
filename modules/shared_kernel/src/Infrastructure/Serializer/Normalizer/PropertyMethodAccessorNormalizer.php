<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\Serializer\Normalizer;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionObject;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

final class PropertyMethodAccessorNormalizer extends AbstractObjectNormalizer
{
    /**
     * @throws ReflectionException
     */
    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return parent::supportsNormalization($data, $format) && $this->supports($data::class);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return false;
    }

    /**
     * @param mixed[] $context
     * @return string[]
     */
    protected function extractAttributes(object $object, ?string $format = null, array $context = []): array
    {
        $reflectionObject = new ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(ReflectionMethod::IS_PUBLIC);

        $attributes = [];
        foreach ($reflectionMethods as $method) {
            if (! $this->isAllowedMethod($method)) {
                continue;
            }

            if (! $this->isAllowedAttribute($object, $method->name, $format, $context)) {
                continue;
            }

            $attributes[] = $method->name;
        }

        return $attributes;
    }

    /**
     * @param mixed[] $context
     */
    protected function getAttributeValue(object $object, string $attribute, ?string $format = null, array $context = []): mixed
    {
        if (is_callable([$object, $attribute])) {
            return $object->$attribute(); // @phpstan-ignore-line
        }

        return null;
    }

    /**
     * @param mixed[] $context
     */
    protected function setAttributeValue(object $object, string $attribute, mixed $value, ?string $format = null, array $context = []): void
    {
        throw new DenormalizationNotSupportedException();
    }

    /**
     * @param class-string $class
     * @throws ReflectionException
     */
    private function supports(string $class): bool
    {
        $class = new ReflectionClass($class);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->isAllowedMethod($method)) {
                return true;
            }
        }

        return false;
    }

    private function isAllowedMethod(ReflectionMethod $method): bool
    {
        return ! $this->isGetMethod($method) && $this->isAnAccessMethod($method);
    }

    private function isGetMethod(ReflectionMethod $method): bool
    {
        $methodLength = strlen($method->name);

        return ! $method->isStatic() &&
            (
                ((str_starts_with($method->name, 'get') && 3 < $methodLength) ||
                 (str_starts_with($method->name, 'is') && 2 < $methodLength) ||
                 (str_starts_with($method->name, 'has') && 3 < $methodLength)) &&
                $method->getNumberOfRequiredParameters() === 0
            );
    }

    private function isAnAccessMethod(ReflectionMethod $method): bool
    {
        return $method->hasReturnType() &&
            $method->getNumberOfParameters() === 0 &&
            $method->getReturnType() instanceof ReflectionNamedType &&
            $method->getReturnType()->getName() !== 'void';
    }
}
