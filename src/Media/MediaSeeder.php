<?php

namespace Seedling\Media;

use Seedling\Media\ExternalMediaProviders\Picsum;

class MediaSeeder
{
    private const PICSUM = 'picsum';

    /**
     * @param string $provider
     * @param int $mediaItems
     * @param array $args
     * @return array
     *
     * $args[
     * 'width' => 200,
     * 'height' => 500,
     * ]
     */
    public static function generate(string $provider = self::PICSUM, int $mediaItems = 1, array $args = []): array
    {
        $generatedArray = [];
        for ($i = 0; $i < $mediaItems; $i++) {

            switch ($provider) {
                case self::PICSUM:
                    $mediaProvider = new Picsum();
                    $generatedArray[] = $mediaProvider->get($args);
                    break;

                // @todo-nice-to-have: more additional external mediaSources
                // @todo-nice-to-have: add custom own MediaSource on a per-project basis
            }
        }
        return $generatedArray;
    }



}
