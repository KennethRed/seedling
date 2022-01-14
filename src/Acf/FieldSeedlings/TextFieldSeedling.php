<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\DefaultAttribute;
use Seedling\Acf\FieldSeedlings\Traits\SeedlingFaker;

class TextFieldSeedling
{
    use DefaultAttribute, SeedlingFaker;

    /**
     * @param $field
     * @return mixed|string
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

        $faker = self::faker();

        // lets respect the max length.
        return $faker->realText($field['maxlength'] ?: 100);
    }

}
