<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Infrastructure\Serializer\Normalizer;

use Collector\SharedKernel\Infrastructure\Serializer\Normalizer\PropertyMethodAccessorNormalizer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\SerializerInterface;

final class PropertyMethodAccessorNormalizerTest extends TestCase
{
    private PropertyMethodAccessorNormalizer $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $serializer = $this->getMockBuilder(SerializerInterface::class)->getMock();
        $this->sut = new PropertyMethodAccessorNormalizer(
            null,
            null,
            null,
            null,
            null,
            [],
        );
        $this->sut->setSerializer($serializer);
    }

    public function testSupportsNormalization(): void
    {
        $this->assertTrue($this->sut->supportsNormalization($this->dummyObject()));
    }

    public function testSupportsNormalizationOfMixedMethodClass(): void
    {
        $dummy = new class () {
            public function getTest(): string
            {
                return 'test';
            }

            public function test(): string
            {
                return 'test';
            }
        };

        $this->assertTrue($this->sut->supportsNormalization($dummy));
    }

    public function testDoesNotSupportDenormalizationOfAClassWithGettersOnly(): void
    {
        $dummy = new class () {
            public function getTest(): string
            {
                return 'test';
            }
        };

        $this->assertFalse($this->sut->supportsNormalization($dummy));
    }

    public function testDoesNotSupportDenormalization(): void
    {
        $this->assertFalse($this->sut->supportsDenormalization(new stdClass(), 'test'));
    }

    public function testNormalize(): void
    {
        $dummy = $this->dummyObject();

        $normalized = $this->sut->normalize($dummy);

        $this->assertEquals(['foo' => 'foo', 'bar' => 123, 'baz' => false, 'nullable' => null], $normalized);
    }

    private function dummyObject(): object
    {
        return new class () {
            private string $foo = 'foo';
            private int $bar = 123;
            private bool $bool = false;
            private ?string $nullable = null;

            public function foo(): string
            {
                return $this->foo;
            }

            public function bar(): int
            {
                return $this->bar;
            }

            public function baz(): bool
            {
                return $this->bool;
            }

            public function nullable(): ?string
            {
                return $this->nullable;
            }

            /**
             * Getters, Issers and Hassers are handled by a built in Symfony normalizer.
             */
            public function getFooTest(): string
            {
                return 'should not be normalized';
            }

            public function isFooTest(): string
            {
                return 'should not be normalized';
            }

            public function hasFooTest(): string
            {
                return 'should not be normalized';
            }
        };
    }
}
