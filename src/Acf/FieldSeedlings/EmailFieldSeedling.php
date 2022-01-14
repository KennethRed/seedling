<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\SeedlingFaker;

class EmailFieldSeedling
{
    Use SeedlingFaker;

    static function generate($field): string
    {
        $faker = self::faker();
        return $faker->email;
    }
}
