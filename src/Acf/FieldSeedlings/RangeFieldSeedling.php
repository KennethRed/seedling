<?php

namespace Seedling\Acf\FieldSeedlings;

class RangeFieldSeedling
{
    /**
     * @param $field
     * @return mixed|string
     */
    static function generate($field)
    {
        return NumberFieldSeedling::generate($field);
    }
}
