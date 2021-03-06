<?php

namespace Seedling\Commands;

use Seedling\Abstracts\ModelSeederAbstract;
use Seedling\ModelFields;
use WP_CLI;


class ModelSeederCommands
{
    /**
     * @var ModelSeederAbstract $modelSeeder
     */
    protected ModelSeederAbstract $modelSeeder;

    protected string $customPostType;

    /**
     * @param ModelSeederAbstract $modelSeeder
     */
    public function __construct(ModelSeederAbstract $modelSeeder)
    {
        $this->modelSeeder = $modelSeeder;
        $this->customPostType = $this->modelSeeder->type();

        if (class_exists('WP_CLI')) {
            WP_CLI::add_command("seed create model $this->customPostType", array($this, 'create'));
            WP_CLI::add_command("seed clear model $this->customPostType", array($this, 'clear'));
            WP_CLI::add_command("seed list model $this->customPostType", array($this, 'list'));
        }
    }

    /**
     * Creates a page for this post type
     *
     * ## OPTIONS
     *
     * [--limit=<amount>]
     * : amount of items created, defaults to 1
     *
     * [--fresh]
     * : add this tag to remove previously set posts created with this seeder, manually created posts will not be deleted
     *
     * [--f]
     * : identical to --fresh
     *
     * [--freshforce]
     * : add this tag to remove all posts of this model
     *
     * [--ff]
     * : identical to --freshforce
     *
     * [--allow-empty-values]
     * : When this flag is added, when seeding blocks, the field inputs have a 50% chance to be empty. This can be used to
     *  Test end user input. This flag respects the required setting in the field.
     *
     * [--blockfactory=<list>]
     * : Supply a csv list of acf blocks that should be used when running this seeder. This overrides the settable gutenbergBlockTemplate.
     *
     * ## EXAMPLES
     * wp seed create model [your-post-type]
     * wp seed create model [your-post-type] --limit=5 --fresh
     * wp seed create model [your-post-type] --limit=25 --freshforce
     * wp seed create model [your-post-type] --limit=25 --freshforce --allow-empty-values
     * wp seed create model [your-post-type] --limit=25 --fresh --blockFactory="acf/header, acf/usps, acf-mediagallery"
     *
     * @when after_wp_load
     */
    function create($args, $assoc_args)
    {
        if (class_exists('WP_CLI')) {
            $progress = WP_CLI\Utils\make_progress_bar("", $assoc_args['limit'] ?? 1);

            if (isset($assoc_args['fresh']) || isset($assoc_args['f'])) {
                $this->deleteOnlySeededPosts();
            }

            if (isset($assoc_args['freshforce']) || isset($assoc_args['ff'])) {
                $this->deleteAllPosts();
            }

            if (isset($assoc_args['blockfactory'])) {
                $this->modelSeeder->CLIGutenbergBlockFactory = str_replace(" ", "", str_getcsv($assoc_args['blockfactory']));
            }

            for ($i = 0; $i < $assoc_args['limit'] ?? 1; $i++) {
                $this->modelSeeder->allowEmptyValuesInBlockFactory = isset($assoc_args['allow-empty-values']);

                $this->modelSeeder->seedPost();

                $progress->tick();
            }
            $progress->finish();

        }
    }

    /**
     * Deletes posts of this model
     *
     * ## OPTIONS
     *
     * [--force-clear-all]
     * : CAREFUL: add this tag to remove all posts of this model, even if they are not generated by this seeder.
     * I hope you know what you are doing.
     *
     * ## EXAMPLES
     * wp seed clear [post-type]
     * wp seed clear [post-type] --force-clear-all
     *
     * @when after_wp_load
     */
    function clear($args, $assoc_args)
    {
        WP_CLI::log("Clearing all posts.");
        if (isset($assoc_args['--force-clear-all'])) {
            $this->deleteAllPosts();
        } else {
            $this->deleteOnlySeededPosts();
        }

        WP_CLI::success("Done. Exiting");
    }

    /**
     * Retrieves all attributes related to this model
     *
     * @when after_wp_load
     */
    function list($args, $assoc_args)
    {
        $fields = ModelFields::getFieldGroupsByPostType($this->customPostType);

        if (!$fields || count($fields) == 0) {
            WP_CLI::error("No fields found for $this->customPostType");
            return;
        }

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? "";
            $fieldType = $field['type'] ?? "";
            WP_CLI::log("fieldname: $fieldName, fieldtype: $fieldType");
        }
    }

    /**
     * Find and delete all items in this model that are created by this seeder.
     */
    private function deleteOnlySeededPosts()
    {
        foreach (get_posts([
            'post_type' => $this->model->type(), 'posts_per_page' => 99999,
            'meta_key' => '_seedling_seeded', 'meta_value' => '1']) as $post) {
            wp_delete_post($post->ID, true);
        }

        WP_CLI::log("Deleted All previously seeded " . $this->model->type() . " Posts...");
    }

    /**
     * Find and delete all items in this model.
     */
    private function deleteAllPosts()
    {
        foreach (get_posts(['post_type' => $this->model->type(), 'posts_per_page' => 99999]) as $post) {
            wp_delete_post($post->ID, true);
        }

        WP_CLI::log("Deleted All " . $this->model->type() . " Posts...");
    }
}
