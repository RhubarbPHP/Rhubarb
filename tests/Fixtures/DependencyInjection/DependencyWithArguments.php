<?php

namespace Rhubarb\Crown\Tests\Fixtures\DependencyInjection;

class DependencyWithArguments extends OneDependency
{
    public $argument1;
    public $argument2;

    public function __construct(SimpleClass $simpleClass, $argument1, $argument2)
    {
        parent::__construct($simpleClass);

        $this->argument1 = $argument1;
        $this->argument2 = $argument2;
    }
}