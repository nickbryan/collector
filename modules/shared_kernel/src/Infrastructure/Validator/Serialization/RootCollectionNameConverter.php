<?php

declare(strict_types=1);

namespace Collector\SharedKernel\Infrastructure\Validator\Serialization;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class RootCollectionNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName): string
    {
        // validationData[create][0][email] to create][0][email
        $propertyName = preg_replace('/^validationData\[(.+)\]$/', '${1}', $propertyName);

        if ($propertyName === null) {
            throw new SerializationException(sprintf('Unable to remove root collection name: %s.', preg_last_error()));
        }

        // create][0][email to create[0].email
        $propertyName = preg_replace('/](\[[0-9A-Za-z:_]+\])\[/', '${1}.', $propertyName);

        if ($propertyName === null) {
            throw new SerializationException(sprintf('Unable transform to collection accessor: %s.', preg_last_error()));
        }

        return $propertyName;
    }

    public function denormalize(string $propertyName): string
    {
        return $propertyName;
    }
}
