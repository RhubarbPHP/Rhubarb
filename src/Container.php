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

    /**
     * Returns the current container for the running application
     */
    public final static function current()
    {
        return Application::current()->container();
    }

    public final function registerClass($classRequested, $classToInstantiate, $singleton = false)
    {
        $this->concreteClassMappings[($singleton ? "_" : "").$classRequested] = $classToInstantiate;

        if (!$singleton){
            unset($this->concreteClassMappings["_".$classRequested]);
            unset($this->singletons[$classRequested]);
        }
    }

    /**
     * Creates an object instance of the requested class, optionally passing additional constructor arguments
     *
     * @param $requestedClass
     * @param ...$arguments
     * @return mixed|object
     */
    public final function getInstance($requestedClass, ...$arguments)
    {
        if (isset($this->singletons[$requestedClass])){
            return $this->singletons[$requestedClass];
        }

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
     * Creates an object instance of the requested class from the current DI container
     *
     * @see getInstance()
     * @param $requestedClass
     * @param ...$arguments
     * @return mixed
     */
    public static function instance($requestedClass, ...$arguments)
    {
        $instance = Application::current()->container();
        return call_user_func_array([$instance, "getInstance"], func_get_args());
    }

    /**
     * Creates a singleton object instance of the requested class from the current DI container
     *
     * @see getInstance()
     * @param $requestedClass
     * @param ...$arguments
     * @return mixed
     */
    public function registerSingleton($requestedClass, callable $singletonCreationCallback)
    {
        if (!isset($this->singletons[$requestedClass])){
            $singleton = $singletonCreationCallback();
            $this->singletons[$requestedClass] = $singleton;
            $this->concreteClassMappings["_".$requestedClass] = $requestedClass;
        }

        return $this->singletons[$requestedClass];
    }

    /**
     * Deregisters a stored singleton.
     *
     * @param $requestedClass
     */
    public function clearSingleton($requestedClass)
    {
        unset($this->singletons[$requestedClass]);
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

            $dependency = $this->getInstance($dependencyClass->getName());
            $paramArgs[] = $dependency;
        }

        $paramArgs = array_merge($paramArgs,$arguments);

        $instance = $constructor->getDeclaringClass()->newInstanceArgs($paramArgs);

        return $instance;
    }
}