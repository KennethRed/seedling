<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

trait MultipleAttribute
{
    static function multiple($field)
    {
        if (!isset($field['multiple'])) {
            return false;
        }

        if ($field['multiple'] == "") {
            return false;
        }

        return $field['multiple'] ?? false;
    }
}
