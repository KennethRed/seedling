<?php

namespace Seedling\Traits;

use Exception;
use Seedling\Abstracts\ModelSeederAbstract;
use Seedling\Abstracts\TaxonomySeederAbstract;

trait HierarchicalTrait
{
    /**
     * @throws Exception
     */
    public function retrievePossibleParent(): int
    {
        if (!$this->hierarchical()) {
            return false;
        }


        if($this instanceof TaxonomySeederAbstract){
            return $this->returnPossibleTaxonomyParent();
        }

        if($this instanceof ModelSeederAbstract){
            return $this->returnPossibleModelParent()->ID ?? false;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function returnPossibleTaxonomyParent(){
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
    private function returnPossibleModelParent()
    {
        // get count of current type of taxonomies
        $models = get_posts(['post_type' => $this->type(), 'posts_per_page' => 99999]);

        if (isset($models) and !is_wp_error($models)
            and is_array($models) and !empty($models)) {

            $totalModels = count($models);

            // there is a 25% chance that we will return a parent, 75% chance of false.
            return $models[random_int(0, $totalModels * 4)] ?? false;
        }

        return false;
    }
}
