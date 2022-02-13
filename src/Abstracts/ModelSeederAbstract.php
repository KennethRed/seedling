<?php

namespace Seedling\Abstracts;

use Exception;
use Faker\Factory;
use Illuminate\Support\Str;

use WP_Post;

use Seedling\Acf\Acf;
use Seedling\SeedlingConfigurator;

use Seedling\Interfaces\ConfigInterface;
use Seedling\Interfaces\HierarchicalInterface;
use Seedling\Interfaces\TypeInterface;

use Seedling\Traits\AcfFieldSeederTrait;
use Seedling\Traits\HierarchicalTrait;

use Seedling\Commands\ModelSeederCommands;


abstract class ModelSeederAbstract implements TypeInterface, ConfigInterface, HierarchicalInterface
{
    use AcfFieldSeederTrait;
    use HierarchicalTrait;

    /**
     * @var bool
     *
     * Allows for empty values to be stored when creating entries.
     */
    public bool $allowEmptyValuesInBlockFactory = false;

    private SeedlingConfigurator $seedlingConfigurator;

    /**
     * @var array|string|string[]
     */
    public $CLIGutenbergBlockFactory;

    public function __construct(SeedlingConfigurator $seedlingConfigurator)
    {
        $this->seedlingConfigurator = $seedlingConfigurator;
        new ModelSeederCommands($this);
    }

    /**
     * @return array
     *
     * specify configuration settings for this seeder.
     */
    public function config(): array
    {
        return [];
    }

    private function seedlingMode(): string
    {
       return $this->config()['seedlingMode'] ?? $this->seedlingConfigurator->seedlingDefaultMode();
    }

    public function limit(): string
    {
        return $this->config()['limit'] ?? $this->seedlingConfigurator->defaultLimit();
    }

    /**
     * @return array
     *
     * Provide a list of gutenberg blocks
     */
    public function gutenbergBlocks(): array
    {
        return [];
    }

    /**
     * @return array
     *
     * post_title : string
     * post_content : string
     * post_status : publish|future|draft|pending|private|trash
     * post_type : string
     * post_author : string based on id of user
     * post_category : array of categories
     * page_template : string (template name)
     * @throws Exception
     */
    public function factory(): array
    {
        $faker = Factory::create();

        return array(
            'post_title' => $faker->text(50),
            'post_content' => $this->generateGutenbergBlockSnippet(),
            'post_status' => "publish",
            'post_type' => $this->type(),
            'post_parent' => $this->retrievePossibleParent(),
        );
    }

    /**
     * @throws Exception
     */
    public function seedPost()
    {
        $post = wp_insert_post($this->factory());
        $postObject = WP_Post::get_instance($post);
        update_field('_seedling_seeded', '1', $post);

        if ($this->isAcfFactoryNotEmpty()) {
            $this->seedAcfWithFactory($postObject);
        }

        // When no manual Acf Factory is defined in the model we fall back to seeding all fields.
        if(!$this->isAcfFactoryNotEmpty()){
            $this->seedAcf($postObject);
        }

        return $post;
    }

    /**
     * @throws Exception
     */
    private function generateGutenbergBlockSnippet(): string
    {
        $output = "";

        /*
         * here we should loop through all blocks present in the modelGutenbergBlocKSeederSetup function,
         * load all fields with dummy data and return a WP-Gutenberg usable object.
         */

        foreach ($this->CLIGutenbergBlockFactory ?? $this->gutenbergBlocks() as $blockName) {

            $attrs = [];
            $blockName = esc_html($blockName);
            $attrs['name'] = $blockName;

            // Setting the id is required, otherwise values won't be shown
            // when viewing the page without manually saving.
            $attrs['id'] = "block_" . Str::lower(Str::random(8));

            $blockFields = acf_get_block_fields(['name' => $blockName]);

            /*
             * Dynamically set value for each first-level acf field based on type.
             * @todo: check if block already has 'demo-data' set, if so, use that data instead
             */
            foreach ($blockFields as $field) {
                /*
                 * When allowEmptyValuesInBlockFactory is true (either via the model or via command)
                 * we toss a coin to determine of the value should be filled, or should be left empty.
                 * This does respect the required setting of the field.
                 */

                if ($this->allowEmptyValuesInBlockFactory && !$field['required'] && rand(0, 1) == 1) {
                    continue;
                }

                $attrs["data"][$field['key']] = Acf::generateFieldData($field) ?: "";
            }

            $blockStringData = '<!-- wp:' . $blockName . ' ' . acf_json_encode($attrs) . ' /--> ';

            $blockStringData = addslashes(
                preg_replace_callback(
                    '/<!--\s+wp:(?P<name>[\S]+)\s+(?P<attrs>{[\S\s]+?})\s+(?P<void>\/)?-->/',
                    'acf_parse_save_blocks_callback',
                    $blockStringData
                ));


            $output .= $blockStringData;

        }
        return $output;
    }

    /**
     * @return bool
     *
     * First we check the config array if hierarchical is set
     * if not found we then check the post Type object itself for a hierarchical tag.
     *
     */
    public function hierarchical(): bool
    {
        if(isset($this->config()['hierarchical'])){
            return $this->config()['hierarchical'];
        }

        $postTypeObject = get_post_type_object($this->type());

        return $postTypeObject->hierarchical ?? false;
    }
}
