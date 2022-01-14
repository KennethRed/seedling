<?php

namespace Seedling\Acf\FieldSeedlings;

use Exception;
use Seedling\Acf\FieldSeedlings\Traits\MaxAttribute;
use Seedling\Acf\FieldSeedlings\Traits\MinAttribute;
use WP_CLI;

class RelationshipFieldSeedling
{
    use MinAttribute, MaxAttribute;

    /**
     * @throws Exception
     */
    static function generate($field): array
    {
        // Check if there are enough related Posts for the minimal amount of posts
        if (!count(self::relationPostList($field, 999)) > (self::min($field))) {

            // @todo-nice-to-have: Perhaps trigger related seeder so it does not fail at this point

            if (class_exists('WP_CLI')) {
                WP_CLI::error("No or not enough related posts available.
At least " . self::min($field) . " are required.
This value originates from the minimum value set in the relationship field " . $field['key'] . ': ' . $field['name'] . ".
This can be fixed by either specifying the desired post type on the ACF relationship field or
by first running a related Seedling first on:
" . (implode(", ", $field['post_type']) ?: "any post as no relationship post type is selected"));
            }
        }

        $posts = array_map(
            function ($o) {
                return $o->ID;
            },
            self::relationPostList($field));

        return array_splice($posts, random_int(self::min($field), self::max($field) ?: 8));
    }

    /**
     *
     * Returns a list of all possible post types that are selected in this relationship field, returns a list of 10 items.
     */
    static function relationPostList(array $field, $max = 9): array
    {
        $args = [
            'posts_per_page' => $max,
            'post_type' => 'page'
        ];

        //@ Todo:
        // load posts based on selected taxonomy
        //        if (!empty($field['taxonomy'])) {
        //            $args[] = [
        //                'tax_query' => []
        //            ];
        //        }

        if (!empty($field['post_type'])) {
            $args['post_type'] = $field['post_type'];
        }

        return get_posts($args);
    }
}
