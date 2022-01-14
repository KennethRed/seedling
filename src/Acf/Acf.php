<?php

namespace Seedling\Acf;

use Exception;
use Seedling\Acf\FieldSeedlings\CheckboxFieldSeedling;
use Seedling\Acf\FieldSeedlings\EmailFieldSeedling;
use Seedling\Acf\FieldSeedlings\ImageFieldSeedling;
use Seedling\Acf\FieldSeedlings\LinkFieldSeedling;
use Seedling\Acf\FieldSeedlings\NumberFieldSeedling;
use Seedling\Acf\FieldSeedlings\PasswordFieldSeedling;
use Seedling\Acf\FieldSeedlings\RadioFieldSeedling;
use Seedling\Acf\FieldSeedlings\RangeFieldSeedling;
use Seedling\Acf\FieldSeedlings\RelationshipFieldSeedling;
use Seedling\Acf\FieldSeedlings\SelectFieldSeedling;
use Seedling\Acf\FieldSeedlings\TextAreaFieldSeedling;
use Seedling\Acf\FieldSeedlings\TextFieldSeedling;
use Seedling\Acf\FieldSeedlings\UrlFieldSeedling;
use Seedling\Acf\FieldSeedlings\WyswiwygFieldSeedling;


class Acf
{
    /**
     * @throws Exception
     */
    static function generateFieldData($field)
    {
        switch ($field['type']) {

            case('text'):
                return TextFieldSeedling::generate($field);

            case('textarea'):
                return TextAreaFieldSeedling::generate($field);

            case('image'):
                return ImageFieldSeedling::generate($field)['id'];

            case('link'):
                return LinkFieldSeedling::generate($field);

            case('relationship'):
                return RelationshipFieldSeedling::generate($field);

            case('password'):
                return PasswordFieldSeedling::generate($field);

            case('email'):
                return EmailFieldSeedling::generate($field);

            case('url'):
                return UrlFieldSeedling::generate($field);

            case('wysiwyg'):
                return WyswiwygFieldSeedling::generate($field);

            case('number'):
                return NumberFieldSeedling::generate($field);

            case('range'):
                return RangeFieldSeedling::generate($field);

            case('select'):
                return SelectFieldSeedling::generate($field);

            case('radio'):
                return RadioFieldSeedling::generate($field);

            case('checkbox'):
                return CheckboxFieldSeedling::generate($field);

//            case('group'):
////                dd($field);
////                foreach($field['sub_fields'] as $field){
////                    acf_update_value()
////                }
//                return CheckboxFieldSeedling::generate($field);

            default:
                return false;
        }
    }
}
