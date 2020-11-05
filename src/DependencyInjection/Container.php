<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\DependencyInjection;

use Psr\Container\ContainerInterface;
use ReflectionMethod;
use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\ClassMappingException;

/**
 * Rhubarb's dependency container
 */
final class Container implements ContainerInterface
{
    private $concreteClassMappings = [];

    private $singletons = [];

    public function get(string $id)
    {
        return $this->getInstance($id);
    }

    public function has(string $id)
    {
        if (!class_exists($id)){
            // Not a class so we have to look into the mappings.
            if (!isset($this->concreteClassMappings[$id]) && !isset($this->singletons[$id])){
                return false;
            }
        }
        
        return true;
    }

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

        if ($reflection->isAbstract()){
            throw new ClassMappingException($class);
        }

        $constructor = $reflection->getConstructor();

        if ($constructor == null){
            // No defined constructor so exit simply with a new instance.
            $instance = $reflection->newInstanceArgs($arguments);
        } else {
            $instance = $this->generateInstanceFromConstructor($reflection, $constructor, $arguments);
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
        return self::current()->getInstance($requestedClass, ...$arguments);
    }

    /**
     * Creates a singleton object instance of the requested class from the current DI container
     *
     * @see getInstance()
     * @param $requestedClass
     * @param ...$arguments
     * @return mixed
     */
    public static function singleton($requestedClass, ...$arguments)
    {
        return self::current()->getSingleton($requestedClass, ...$arguments);
    }

    /**
     * Creates a singleton object instance of the requested class from the current DI container
     *
     * @see getInstance()
     * @param $requestedClass
     * @param ...$arguments
     * @return mixed
     */
    public function getSingleton($requestedClass, callable $singletonCreationCallback = null)
    {
        if (!isset($this->singletons[$requestedClass])){
            if ($singletonCreationCallback){
                $singleton = $singletonCreationCallback;
            } else {
                $singleton = $this->instance($requestedClass);
            }

            $this->singletons[$requestedClass] = $singleton;
            $this->concreteClassMappings["_".$requestedClass] = $requestedClass;
        }

        if (is_callable($this->singletons[$requestedClass])){
            $callback = $this->singletons[$requestedClass];
            $this->singletons[$requestedClass] = $callback();
        }

        return $this->singletons[$requestedClass];
    }

    /**
     * Registers an instance, or a callback to create an instance, for a singleton.
     *
     * @param string $requestedClass The class of the singleton
     * @param mixed $instanceOrCallback An object instance or a callable
     */
    public function registerSingleton($requestedClass, $instanceOrCallback)
    {
        $this->singletons[$requestedClass] = $instanceOrCallback;
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
    private function generateInstanceFromConstructor($requestedClass, $constructor, $arguments)
    {
        $params = $constructor->getParameters();
        $paramArgs = [];

        foreach ($params as $param) {
            $dependencyClass = $param->getClass();
            if ($dependencyClass == null) {
                // End of the type hinted arguments
                break;
            }

            $dependencyClassName = $dependencyClass->getName();

            if (count($arguments) > 0 && is_object($arguments[0]) && $arguments[0] instanceof $dependencyClassName) {
                $dependency = $arguments[0];
                array_splice($arguments, 0, 1);
            } else {
                $dependency = $this->getInstance($dependencyClass->getName());
            }

            $paramArgs[] = $dependency;
        }

        $paramArgs = array_merge($paramArgs,$arguments);

        $instance = $requestedClass->newInstanceArgs($paramArgs);

        return $instance;
    }
}