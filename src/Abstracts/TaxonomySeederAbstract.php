<?php

namespace Seedling\Abstracts;

use Exception;
use Faker\Factory;
use WP_Taxonomy;
use WP_Term;

use Seedling\Commands\TaxonomySeederCommands;
use Seedling\SeedlingConfigurator;

use Seedling\Interfaces\ConfigInterface;
use Seedling\Interfaces\HierarchicalInterface;
use Seedling\Interfaces\TypeInterface;

use Seedling\Traits\AcfFieldSeederTrait;
use Seedling\Traits\HierarchicalTrait;

abstract class TaxonomySeederAbstract implements TypeInterface, ConfigInterface, HierarchicalInterface
{
    use AcfFieldSeederTrait, HierarchicalTrait;

    private SeedlingConfigurator $seedlingConfigurator;

    public function __construct(SeedlingConfigurator $seedlingConfigurator)
    {
        $this->seedlingConfigurator = $seedlingConfigurator;
        new TaxonomySeederCommands($this);
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
     * @throws Exception
     */
    public function factory(): array
    {
        $faker = Factory::create();

        return [
            'term' => $this->nameDuplicateHandler($faker->word()),
            'args' => [
                'description' => $faker->text(50),
                'parent' => $this->retrievePossibleParent()
            ]
        ];
    }

    private function nameDuplicateHandler($name): string
    {
        if (get_term_by('name', $name, $this->type()) instanceof WP_Term) {
            // A duplicate.
            $name = $this->nameDuplicateHandler($this->regenerateName($name));
        }

        return $name;
    }

    private function regenerateName($name): string
    {
        $faker = Factory::create();
        return "$name $faker->word";
    }

    /**
     * @return WP_Term
     * @throws Exception
     */
    public function seedTerm(): WP_Term
    {
        /** @var WP_Term $term */
        $term = wp_insert_term($this->factory()['term'], $this->type(), $this->factory()['args']);
        $termObject = WP_Term::get_instance($term['term_id'], $this->type());

        update_field('_seedling_seeded', '1', "term_" . $termObject->term_id);

        if ($this->isAcfFactoryNotEmpty()) {
            $this->seedAcfWithFactory($termObject);
        }

        // When no manual Acf Factory is defined in the model we fall back to seeding all fields.
        if (!$this->isAcfFactoryNotEmpty()) {
            $this->seedAcf($termObject);
        }

        return $termObject;
    }

    /**
     * @return bool
     *
     * First we check the config array if hierarchical is set
     * if not found we then check the taxonomy settings itself for a hierarchical tag.
     *
     */
    public function hierarchical(): bool
    {
        if(isset($this->config()['hierarchical'])){
            return $this->config()['hierarchical'];
        }

        /** @var WP_Taxonomy $taxonomy */
        $taxonomy = get_taxonomy($this->type());

        return $taxonomy->hierarchical ?? false;
    }

}
