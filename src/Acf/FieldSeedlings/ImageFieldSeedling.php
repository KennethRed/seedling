<?php

namespace Seedling\Acf\FieldSeedlings;

use Seedling\Media;

class ImageFieldSeedling
{
    /**
     * @param $field
     * @return array|false
     */
    static function generate($field)
    {
        return Media\Media::getRandomImageObjectFromMediaGallery();
    }
}
