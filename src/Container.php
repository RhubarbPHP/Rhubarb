<?php

namespace Rhubarb\Crown;

use ReflectionMethod;

/**
 * Rhubarb's dependency container
 */
final class Container
{
    private $concreteClassMappings = [];

    private $singletons = [];

    public final function registerClass($classRequested, $classToInstantiate, $singleton = false)
    {
        $this->concreteClassMappings[($singleton ? "_" : "").$classRequested] = $classToInstantiate;
    }

    public final function instance($requestedClass, ...$arguments)
    {
        $useSingleton = false;

        // Check for singletons first as they should trump previous registrations of non
        // singleton mappings.
        if (isset($this->concreteClassMappings['_'.$requestedClass])) {
            $class = $this->concreteClassMappings['_'.$requestedClass];
            $useSingleton = true;
        } elseif (isset($this->concreteClassMappings[$requestedClass])){
            $class = $this->concreteClassMappings[$requestedClass];
        } else {
            $class = $requestedClass;
        }

        if ($useSingleton && isset($this->singletons[$requestedClass])){
            return $this->singletons[$requestedClass];
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor == null){
            // No defined constructor so exit simply with a new instance.
            $instance = $reflection->newInstanceArgs($arguments);
        } else {
            $instance = $this->generateInstanceFromConstructor($constructor, $arguments);
        }

        if ($useSingleton) {
            $this->singletons[$requestedClass] = $instance;
        }

        return $instance;
    }

    /**
     * @param ReflectionMethod $constructor
     * @return mixed
     */
    private function generateInstanceFromConstructor($constructor, $arguments)
    {
        $params = $constructor->getParameters();
        $paramArgs = [];

        foreach ($params as $param) {
            $dependencyClass = $param->getClass();
            if ($dependencyClass == null) {
                // End of the type hinted arguments
                break;
            }

            $dependency = $this->instance($dependencyClass->getName());
            $paramArgs[] = $dependency;
        }

        $paramArgs = array_merge($paramArgs,$arguments);

        $instance = $constructor->getDeclaringClass()->newInstanceArgs($paramArgs);

        return $instance;
    }
}