<?php

namespace Seedling;

class ModelFields
{
    static function getFieldGroupsByPostType($postType): array
    {
        $fields = [];
        $groups = acf_get_field_groups(array('post_type' => $postType));

        // Loop over results and append fields.
        if ( $groups ) {
            foreach ( $groups as $field_group ) {
                $fields = array_merge( $fields, acf_get_fields( $field_group ) );
            }
        }

       return $fields;
    }

    static function getFieldGroupsByTaxonomy($taxonomy): array
    {
        $fields = [];
        $groups = acf_get_field_groups(['taxonomy' => $taxonomy]);

        // Loop over results and append fields.
        if ( $groups ) {
            foreach ( $groups as $field_group ) {
                $fields = array_merge( $fields, acf_get_fields( $field_group ) );
            }
        }

        return $fields;
    }
}
