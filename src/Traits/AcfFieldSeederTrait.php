<?php

namespace Seedling\Traits;

use Exception;
use Seedling\Acf\Acf;
use Seedling\ModelFields;
use WP_Post;
use WP_Term;

trait AcfFieldSeederTrait
{

    /**
     * acfFactory allows for custom key value based seeder data.
     *
     * @return array
     */
    public function acfFactory(): array
    {
        return [];
    }

    private function isAcfFactoryNotEmpty(): bool
    {
        return count($this->acfFactory()) > 0;
    }

    private function seedAcfWithFactory($object)
    {
        foreach ($this->acfFactory() as $key => $value) {

            if ($object instanceof WP_Term) {
                update_field($key, $value, "term_" . $object->term_id);
            }

            if ($object instanceof WP_Post) {
                update_field($key, $value, $object);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function seedAcf($object)
    {
        if ($object instanceof WP_Term) {
            $fields = ModelFields::getFieldGroupsByTaxonomy($this->type());

            foreach ($fields as $field) {
                update_field($field['name'], Acf::generateFieldData($field), "term_" . $object->term_id);
            }
        }

        if ($object instanceof WP_Post) {
            $fields = ModelFields::getFieldGroupsByPostType(get_post_type($object->ID));

            foreach ($fields as $field) {
                update_field($field['name'], Acf::generateFieldData($field), $object->ID);
            }
        }


    }
}
