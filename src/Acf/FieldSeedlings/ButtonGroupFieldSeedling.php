<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\ChoicesAttribute;
use Seedling\Acf\FieldSeedlings\Traits\DefaultAttribute;
use Seedling\Acf\FieldSeedlings\Traits\MultipleAttribute;
use Seedling\Acf\FieldSeedlings\Traits\SeedlingFaker;

class ButtonGroupFieldSeedling
{
    use DefaultAttribute, ChoicesAttribute, MultipleAttribute, SeedlingFaker;

    /**
     * @param $field
     * @return array|false|mixed
     */
    static function generate($field)
    {
        /*
        * When generating this field it sometimes a has a default value.
        * If this default value is present we return that value instead of returning faker data.
        */
        if (self::default($field)) {
            return self::default($field);
        }

        if (!self::choices($field)) {
            return false;
        }

        $faker = self::faker();
        return $faker->randomElement(self::choices($field));
    }
}
