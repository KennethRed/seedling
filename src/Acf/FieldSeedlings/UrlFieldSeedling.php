<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\DefaultAttribute;
use Seedling\Acf\FieldSeedlings\Traits\SeedlingFaker;

class UrlFieldSeedling
{
    use DefaultAttribute, SeedlingFaker;

    static function generate($field): string
    {
        /*
        * When generating this field it sometimes a has a default value.
        * If this default value is present we return that value instead of returning faker data.
        */
        if (self::default($field)) {
            return self::default($field);
        }

        $faker = self::faker();
        return $faker->url;
    }
}
