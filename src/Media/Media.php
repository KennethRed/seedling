<?php

namespace Seedling\Media;

class Media
{
    static function getRandomImageObjectFromMediaGallery()
    {
        // get all image ids available
        $image_ids = get_posts(
            array(
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'post_status' => 'inherit',
                'posts_per_page' => -1,
                'fields' => 'ids',
            )
        );

        if(count($image_ids) == 0 ){
            $image_ids = MediaSeeder::generate();
        }

        $random_index = rand(0, count($image_ids)-1);
        $randomImage = $image_ids[$random_index];
        return acf_get_attachment($randomImage);
    }

    static function getImageArrayFromMediaGallery($max = 5): array
    {
        for ($i = 1; $i < $max; $i++) {
            $imageArray[] = self::getRandomImageObjectFromMediaGallery();
        }

        return $imageArray ?? [];
    }
}
