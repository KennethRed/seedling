<?php

namespace Seedling\Abstracts;

use Seedling\Commands\OptionsSeederCommands;
use Seedling\Traits\AcfFieldSeederTrait;

abstract class OptionSeederAbstract
{
    use AcfFieldSeederTrait;
    /**
     * @return string
     *
     * Should be equal to the options name that you want to create
     */
    abstract function optionName(): string;

    public function __construct()
    {
        new OptionsSeederCommands($this);
    }

    public function options(): array
    {
        return [];
    }

    public function seed()
    {
        $this->seedAcfFields('option');
    }

}
