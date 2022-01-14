<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

trait DefaultAttribute
{
    static function default($field)
    {
        if (!isset($field['default_value'])) {
            return false;
        }

        if ($field['default_value'] == "") {
            return false;
        }

        return $field['default_value'] ?? false;
    }
}
