<?php

namespace Seedling;

use Seedling\Abstracts\ModelSeederAbstract;

use WP_CLI;

class SeedlingConfigurator
{
    const SEEDLING_APP_CONFIG_GROUP_NAME = "seedlingApp";

    const SEEDLING_MODE_FILL_ALL = "fill_all";
    const SEEDLING_MODE_FILL_REQUIRED = "fill_required";
    const SEEDLING_MODE_FILL_RANDOM_RESPECT_REQUIRED = "fill_random_respect_required";
    const SEEDLING_MODE_FILL_RANDOM_DISREGARD_REQUIRED = "fill_random_disregard_required";

    const SEEDLING_MODES = [
        self::SEEDLING_MODE_FILL_ALL,
        self::SEEDLING_MODE_FILL_REQUIRED,
        self::SEEDLING_MODE_FILL_RANDOM_RESPECT_REQUIRED,
        self::SEEDLING_MODE_FILL_RANDOM_DISREGARD_REQUIRED
    ];

    const IGNORED_POST_TYPES = [
        'attachment',
    ];

    public array $config;

    private array $modelSeeders = [];

    private array $buildCommands = [];

    public function __construct($config = [])
    {
        $this->config = $config;
        $this->initialize();
    }

    private function postTypes()
    {
        return get_post_types(['public' => true]);
    }

    private function initialize()
    {
        if (class_exists('WP_CLI')) {

            foreach ($this->postTypes() as $postType) {
                $modelSeeder = $this->generateAnonymousModelSeeder($this, $postType);
                $limit = $modelSeeder->limit();
                $this->modelSeeders[$postType] = $modelSeeder;
                $this->buildCommands[] = "seed create model $postType --limit=$limit";
            }
            WP_CLI::add_command("seed start", array($this, 'seedStart'));
        }
    }

    /**
     * generates data for the entire site
     *
     * ## OPTIONS
     * [--fresh]
     * : add this tag to remove previously set posts created with this seeder, manually created posts will not be deleted
     *
     * [--f]
     * : identical to --fresh
     *
     * ## EXAMPLES
     * wp seed start --fresh
     * @when after_wp_load
     */
    public function seedStart()
    {
        if (class_exists('WP_CLI')) {

            if (isset($assoc_args['fresh']) || isset($assoc_args['f'])) {

                foreach ($this->postTypes() as $postType){
                    foreach (get_posts([
                        'post_type' => $postType, 'posts_per_page' => 99999,
                        'meta_key' => '_seedling_seeded', 'meta_value' => '1']) as $post) {
                        wp_delete_post($post->ID, true);
                    }
                    WP_CLI::log("Deleted All previously seeded " . $postType . " models...");
                }
            }

            foreach ($this->buildCommands as $buildCommand) {
                WP_CLI::runcommand($buildCommand);
            }
        }
    }

    private function generateAnonymousModelSeeder(SeedlingConfigurator $seedlingConfigurator, $postType): ModelSeederAbstract
    {
        return new class($seedlingConfigurator, $postType) extends ModelSeederAbstract {
            private string $type;
            private SeedlingConfigurator $seedlingConfigurator;

            public function __construct(SeedlingConfigurator $seedlingConfigurator, string $type)
            {
                $this->type = $type;
                $this->seedlingConfigurator = $seedlingConfigurator;
                parent::__construct($seedlingConfigurator);
            }

            public function type(): string
            {
                return $this->type;
            }

            public function config(): array
            {
                return $this->seedlingConfigurator->config['models'][$this->type] ?? [];
            }
        };
    }

    public function seedlingDefaultMode()
    {
        if (isset($this->config[self::SEEDLING_APP_CONFIG_GROUP_NAME]['seedlingDefaultMode'])
            && in_array($this->config[self::SEEDLING_APP_CONFIG_GROUP_NAME]['seedlingDefaultMode'], self::SEEDLING_MODES)) {
            return $this->config[self::SEEDLING_APP_CONFIG_GROUP_NAME]['seedlingDefaultMode'];
        }
        return self::SEEDLING_MODE_FILL_ALL;
    }

    /**
     * @return mixed|null
     */
    public function defaultLimit()
    {
        return $this->config[self::SEEDLING_APP_CONFIG_GROUP_NAME]['defaultLimit'] ?? 10;
    }
}
