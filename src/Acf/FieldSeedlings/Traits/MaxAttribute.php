<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

trait MaxAttribute
{
    static function max($field)
    {
        if (!isset($field['max'])) {
            return false;
        }

        if ($field['max'] == "") {
            return false;
        }

        return $field['max'] ?? false;
    }
}
