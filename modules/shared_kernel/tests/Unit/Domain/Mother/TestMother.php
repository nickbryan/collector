<?php

declare(strict_types=1);

namespace CollectorTest\SharedKernel\Unit\Domain\Mother;

use Faker\Factory;
use Faker\Generator;

abstract class TestMother
{
    protected static function faker(): Generator
    {
        static $generator = null;

        if ($generator === null) {
            $generator = Factory::create();
        }

        return $generator;
    }
}
