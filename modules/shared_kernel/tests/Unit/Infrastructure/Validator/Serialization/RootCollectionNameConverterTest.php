<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Infrastructure\Validator\Serialization;

use Collector\SharedKernel\Infrastructure\Validator\Serialization\RootCollectionNameConverter;
use PHPUnit\Framework\TestCase;

final class RootCollectionNameConverterTest extends TestCase
{
    public function testItDoesNoDenormalization(): void
    {
        $sut = new RootCollectionNameConverter();
        $input = 'validationData[create][0][email]';

        $denormalized = $sut->denormalize($input);

        $this->assertEquals($input, $denormalized);
    }

    public function testItNormalizesTheProperty(): void
    {
        $sut = new RootCollectionNameConverter();

        $normalized = $sut->normalize('validationData[create][0][email][1][nested]');

        $this->assertEquals('create[0].email[1].nested', $normalized);
    }
}
