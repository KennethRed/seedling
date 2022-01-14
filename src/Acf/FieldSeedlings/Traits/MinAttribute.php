<?php

namespace Seedling\Acf\FieldSeedlings\Traits;

trait MinAttribute
{
    static function min($field)
    {
        if (!isset($field['min'])) {
            return false;
        }

        if ($field['min'] == "") {
            return 0;
        }

        return $field['min'] ?? 0;
    }
}
