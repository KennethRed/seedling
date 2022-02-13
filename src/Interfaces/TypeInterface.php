<?php

namespace Seedling\Interfaces;

interface TypeInterface
{
    /**
     * @return string
     *
     * Should be equal to the model type that you want to create
     */
    public function type():string;
}
