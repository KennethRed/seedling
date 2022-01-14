<?php

namespace Seedling\Traits;

use Seedling\Acf\Acf;
use Seedling\ModelFields;

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

    private function seedAcfWithFactory($post)
    {
        foreach ($this->acfFactory() as $key => $value) {
            update_field($key, $value, $post);
        }
    }

    private function seedAcf($postId)
    {
        $fields = ModelFields::getFieldGroupsByPostType(get_post_type($postId));

        foreach($fields as $field){
            update_field($field['name'],  Acf::generateFieldData($field), $postId);
        }
    }
}
