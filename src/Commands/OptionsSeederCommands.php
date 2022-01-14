<?php

namespace Seedling\Commands;

use Seedling\Abstracts\OptionSeederAbstract;
use WP_CLI;

class OptionsSeederCommands
{
    /**
     * @var OptionSeederAbstract $optionSeeder
     */
    protected OptionSeederAbstract $optionSeeder;

    /**
     * @param OptionSeederAbstract $optionSeeder
     */
    public function __construct(OptionSeederAbstract $optionSeeder)
    {
        $this->optionSeeder = $optionSeeder;

        if (class_exists('WP_CLI')) {
            WP_CLI::add_command("seed build options", array($this, 'build'));
            WP_CLI::add_command("seed list options", array($this, 'list'));
        }
    }

    function build($args, $assoc_args)
    {
        $this->optionSeeder->seed('options');
    }

    /**
     * Retrieves all attributes related to this model
     *
     * @when after_acf/init
     */
    function list($args, $assoc_args)
    {
        $fields = [];

        $groups = acf_get_field_groups(['options_page' => $this->optionSeeder->optionName()]);

        foreach ($groups as $field_group) {
            $fields = array_merge($fields, acf_get_fields($field_group));
        }

        if (!$fields) {
            WP_CLI::error("No fields found in options");
            return;
        }

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? "";
            $fieldType = $field['type'] ?? "";
            WP_CLI::log("fieldname: $fieldName, fieldtype: $fieldType");
        }
    }
}
