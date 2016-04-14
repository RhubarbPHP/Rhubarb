<?php

namespace Rhubarb\Crown\Tests\Fixtures\DependencyInjection;

class OneDependency
{
    public $injected;

    public function __construct(SimpleClass $simpleClass)
    {
        $this->injected = $simpleClass;
    }
}