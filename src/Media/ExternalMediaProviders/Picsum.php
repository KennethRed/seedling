<?php

namespace Seedling\Media\ExternalMediaProviders;

use Exception;

class Picsum
{
    function get(array $args): int
    {
        $url = "https://picsum.photos/" . ($args['width'] ?? 2000) . '/'($args['height'] ?? 650);
        $fileName = "picsum".random_int(0,99999);
        return $this->getMediaFromUrl($url, $fileName);
    }

    private function getMediaFromUrl($url, $name): int
    {
        $file = file_get_contents($url);
        return $this->createWPImageObject($file, $name);
    }

    /**
     * @throws Exception
     */
    private function createWPImageObject($file, $name): int
    {
        $uploaddir = wp_upload_dir();
        $uploadfile = $uploaddir['path'] . '/' . $name;

        $contents = file_get_contents($file);

        $savefile = fopen($uploadfile, 'w');
        fwrite($savefile, $contents);
        fclose($savefile);
        $wp_filetype = wp_check_filetype(basename($file), null);

        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => random_int(1000,9999),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $uploadfile);

        $imageMedia = get_post($attach_id);
        $fullSizePath = get_attached_file($imageMedia->ID);
        $attach_data = wp_generate_attachment_metadata($attach_id, $fullSizePath);

        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }

}
