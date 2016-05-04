<?php

namespace Rhubarb\Crown\Exceptions;

class ClassMappingException extends RhubarbException
{
    public function __construct($className, \Exception $previous = null)
    {
        parent::__construct("The class '{$className}' does not have a valid mapping for the DI container.", $previous);
    }
}