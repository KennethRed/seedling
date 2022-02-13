<?php

namespace Seedling\Abstracts;

use Exception;
use Seedling\Commands\TaxonomySeederCommands;
use Seedling\SeedlingConfigurator;
use Seedling\Traits\AcfFieldSeederTrait;
use Faker\Factory;
use WP_Term;

abstract class TaxonomySeederAbstract
{
    use AcfFieldSeederTrait;

    private SeedlingConfigurator $seedlingConfigurator;

    public function __construct(SeedlingConfigurator $seedlingConfigurator)
    {
        $this->seedlingConfigurator = $seedlingConfigurator;
        new TaxonomySeederCommands($this);
    }

    /**
     * @return string
     *
     * Should be equal to the taxonomy type that you want to create
     */
    abstract function type(): string;

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

    public function hierarchical(): bool
    {
        return $this->config()['hierarchical'] ?? false;
    }

    /**
     * @throws Exception
     */
    public function retrievePossibleParent(): int
    {
        if (!$this->hierarchical()) {
            return false;
        }

        // get count of current type of taxonomies
        $terms = get_terms($this->type());

        if (isset($terms) and !is_wp_error($terms)
            and is_array($terms) and !empty($terms)) {

            $totalTerms = count($terms);

            // there is a 25% chance that we will return a parent, 75% chance of false.
            return $terms[random_int(0, $totalTerms * 4)] ?? false;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function factory(): array
    {
        $faker = Factory::create();

        $taxonomyName = $faker->word();

        return [
            'term' => $taxonomyName,
            'args' => [
                'description' => $faker->text(50),
                'parent' => $this->retrievePossibleParent()
            ]
        ];
    }

    /**
     * @return WP_Term
     */
    public function seedTerm(): WP_Term
    {
        /** @var WP_Term $term */
        $term = wp_insert_term($this->factory()['term'], $this->type(), $this->factory()['args']);

        $termObject = WP_Term::get_instance($term['term_id'], $this->type());

        update_field('_seedling_seeded', '1', "term_". $termObject->term_id);

        if ($this->isAcfFactoryNotEmpty()) {
            $this->seedAcfWithFactory($termObject);
        }

        // When no manual Acf Factory is defined in the model we fall back to seeding all fields.
        if (!$this->isAcfFactoryNotEmpty()) {
            $this->seedAcf($termObject);
        }

        return $termObject;
    }

}
