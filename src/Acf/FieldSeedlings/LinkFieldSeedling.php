<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Acf\FieldSeedlings\Traits\SeedlingFaker;

class LinkFieldSeedling
{
    use SeedlingFaker;

    static function generate($field): array
    {
        $faker = self::faker();

        return [
            'title' => $faker->words(2, true),
            'target' => $faker->randomElement(["_self", "_blank"]),
            'url' => "#" . $faker->url
        ];
    }
}
