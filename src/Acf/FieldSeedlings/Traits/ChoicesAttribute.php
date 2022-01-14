<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

trait ChoicesAttribute
{
    static function choices($field)
    {
        if (!isset($field['choices'])) {
            return false;
        }

        if ($field['choices'] == "") {
            return false;
        }

        return $field['choices'] ?? false;
    }
}
