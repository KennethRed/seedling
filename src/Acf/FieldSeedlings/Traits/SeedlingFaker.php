<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

use Faker\Factory;
use Faker\Generator;

trait SeedlingFaker
{
    /**
     * @param bool|string $language
     * @return Generator
     */
    static function faker($language = false): Generator
    {
        return Factory::create($language ?: (get_locale() ?? "en_US"));
    }
}
