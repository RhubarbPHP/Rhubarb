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

namespace Rhubarb\Crown;

use Rhubarb\Crown\UrlHandlers\UrlHandler;
use Symfony\Component\Console\Command\Command;

/**
 * The base class of all modules.
 */
abstract class Module
{
    /**
     * An array of UrlHandlers
     *
     * @var \Rhubarb\Crown\UrlHandlers\UrlHandler[]
     */
    protected $urlHandlers = [];

    /**
     * An array of ResponseFilters
     *
     * @var array
     */
    protected $responseFilters = [];

    /**
     * @var string The name of the module. Normally the module class name minus the word Module
     */
    protected $moduleName = "";

    /**
     * True if the module has been initialised
     *
     * Note that this is not set as you might expect in Initialise() but rather Module::initialiseModules()
     * This is because as Initialise is designed to be overridden the developer might forget to
     * call the parent and the veracity of this boolean is important.
     *
     * @see Initialise()
     * @see InitialiseModules()
     * @var bool
     */
    protected $initialised = false;

    /**
     * Tracks whether url handlers have been registered or not.
     *
     * Used to stop double registration of handlers should a module be initialised twice.
     *
     * @var bool
     */
    protected $urlHandlersRegistered = false;

    /**
     * @var int $version
     */
    protected $version;

    public function __construct()
    {
        $this->moduleName = str_ireplace("Module", "", get_class($this));
    }

    public final function getModuleName()
    {
        return $this->moduleName;
    }

    protected function addUrlHandlers()
    {
        $args = func_get_args();
        $defaultPriority = false;

        if (is_array($args[0])) {
            $handlers = $args[0];

            if (sizeof($args) > 1) {
                $defaultPriority = $args[sizeof($args) - 1];
            }
        } else {
            $handlers = [$args[0] => $args[1]];

            if (sizeof($args) > 2) {
                $defaultPriority = $args[sizeof($args) - 1];
            }
        }

        foreach ($handlers as $url => $handler) {
            if ($defaultPriority !== false) {
                $handler->setPriority($defaultPriority);
            }

            $this->addUrlHandler($url, $handler);
        }
    }

    private function addUrlHandler($url, UrlHandler $handler, $children = [])
    {
        // If $url is numeric then most likely the $handler can report it's own URL
        if (!is_numeric($url)) {
            $handler->setUrl($url);
        }

        $name = $handler->getName();

        if ($name) {
            $this->urlHandlers[$name] = $handler;
        } else {
            $this->urlHandlers[] = $handler;
        }
    }

    /**
     * Should your module require other modules, they should be returned here.
     */
    protected function getModules()
    {
        return [];
    }

    /**
     * Get the finalised collection of url handlers for the module
     *
     * @return UrlHandlers\UrlHandler[]
     */
    protected final function getUrlHandlers()
    {
        if (!$this->urlHandlersRegistered){
            $this->registerUrlHandlers();
            $this->urlHandlersRegistered = true;
        }

        return $this->urlHandlers;
    }

    /**
     * Returns the registered response filters for this module.
     * @return array
     */
    protected function getResponseFilters()
    {
        return $this->responseFilters;
    }

    /**
     * Override to register url handlers by calling AddUrlHandler()
     *
     * This is called after all modules have initialised
     */
    protected function registerUrlHandlers()
    {

    }

    /**
     * Override to execute setups the module requires.
     *
     * Code for module setup must occur here rather than the constructor as this module may get discarded
     * in preference for another instance.
     */
    protected function initialise()
    {
    }

    /**
     * Initialises the module.
     */
    protected final function initialiseModule()
    {
        if (!$this->initialised){
            $this->initialise();
            $this->initialised = true;
        }
    }

    /**
     * An opportunity for the module to return a list custard command line commands to register.
     *
     * Note that modules are asked for commands in the same order in which the modules themselves
     * were registered. This allows extending modules or scaffolds to superseed a command with an
     * improved version by simply reregistering a command with the same name.
     *
     * @return Command[]
     */
    public function getCustardCommands()
    {
        return [];
    }

    /**
     * Used to retrieve the module Version.
     *
     * @return int
     */
    public function getVersion(): int {
        return $this->version;
    }
}