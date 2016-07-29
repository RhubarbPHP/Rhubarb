<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown;

require_once __DIR__ . "/Logging/Log.php";
require_once __DIR__ . "/Request/WebRequest.php";
require_once __DIR__ . "/Response/HtmlResponse.php";
require_once __DIR__ . "/UrlHandlers/UrlHandler.php";

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Exceptions\NonRhubarbException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Request\CliRequest;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UrlHandlers\UrlHandler;
use Symfony\Component\Console\Command\Command;

/**
 * The base class of all modules.
 */
abstract class Module
{
    /**
     * The collection of registered modules.
     *
     * Register a module through RegisterModule
     *
     * @see RegisterModule()
     * @var Module[]
     */
    private static $modules = [];

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
     * True if the autoloader has been registered with PHP
     *
     * @var bool
     */
    protected $autoLoadRegistered = false;

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
     * True indicates we should automatically assume the autoload include will work and ignore
     * the normal fencing as a performance optimisation.
     *
     * @var bool
     */
    protected $optimisticAutoloading = true;

    public function __construct()
    {
        $this->moduleName = str_ireplace("Module", "", get_class($this));
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
     * Clears the modules collection
     *
     * Only really used in unit testing.
     */
    public static function clearModules()
    {
        self::$modules = [];
    }

    /**
     * Should your module require other modules, they should register the module here.
     */
    protected function registerDependantModules()
    {

    }

    /**
     * Register a module with the Core.
     *
     * @static
     * @param Module $module
     */
    public static function registerModule(Module $module)
    {
        // We must register dependant modules first!
        $module->registerDependantModules();

        $moduleClassName = get_class($module);

        // If a module has already been registered the old one should be deregistered and this
        // one registered in its place as it may have settings that superseed the old one.
        if (isset(self::$modules[$moduleClassName])) {
            unset(self::$modules[$moduleClassName]);
        }

        self::$modules[$moduleClassName] = $module;
    }

    /**
     * Returns the collection of Module classes.
     *
     * @static
     * @return \Rhubarb\Crown\Module[]
     */
    public static function getAllModules()
    {
        return self::$modules;
    }

    /**
     * Get's an array of response filters from all registered modules.
     *
     * @return \Rhubarb\Crown\ResponseFilters\ResponseFilter[]
     */
    public static function getAllResponseFilters()
    {
        $modules = self::getAllModules();
        $filters = [];

        foreach ($modules as $module) {
            $filters = array_merge($filters, $module->responseFilters);
        }

        return $filters;
    }

    protected function getUrlHandlers()
    {
        return $this->urlHandlers;
    }

    /**
     * Get's a currently registered URL handler by it's name.
     *
     * Used to modify the URL handlers registered by dependant modules. You should consider the simpler
     * approach of simply re-registering a new handler with the same name.
     *
     * @param $handlerName
     * @return bool|UrlHandler
     */
    protected static function getUrlHandlerByName($handlerName)
    {
        $handlers = self::getAllUrlHandlers();

        if (isset($handlers[$handlerName])) {
            return $handlers[$handlerName];
        }

        return false;
    }

    /**
     * Get's an array of url handlers from all registered modules.
     *
     * @return \Rhubarb\Crown\UrlHandlers\UrlHandler[]
     */
    public static function getAllUrlHandlers()
    {
        $modules = self::getAllModules();
        $handlers = [];

        foreach ($modules as $module) {
            $handlers = array_merge($handlers, $module->getUrlHandlers());
        }

        uasort($handlers, function ($a, $b) {
            $aPriority = $a->getPriority();
            $bPriority = $b->getPriority();

            if ($aPriority == $bPriority) {
                return ($a->getCreationOrder() > $b->getCreationOrder());
            }

            return ($aPriority <= $bPriority);
        });

        return $handlers;
    }

    /**
     * Asks all modules to initialise themselves.
     */
    public static function initialiseModules()
    {
        $modules = self::getAllModules();

        foreach ($modules as $module) {
            if (!$module->initialised) {
                $module->initialise();
                $module->initialised = true;
            }
        }

        foreach ($modules as $module) {
            if (!$module->urlHandlersRegistered) {
                $module->registerUrlHandlers();
                $module->urlHandlersRegistered = true;
            }
        }
    }

    public function clearUrlHandlers()
    {
        $this->urlHandlers = [];
        $this->urlHandlersRegistered = false;
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
     * Generates the response content for the client.
     *
     * This is normally called by platform/execute-http.php and must be called after all
     * modules have been registered to guarantee the correct output.
     *
     * @static
     * @param Request\Request $request
     * @return string|Response
     */
    public static function generateResponseForRequest(Request\Request $request)
    {
        // Set the current request to be this one.
        $context = new Context();
        $context->Request = $request;

        $additionalData = [];
        if ($request instanceof WebRequest) {
            if (!empty($request->GetData)) {
                $additionalData = $request->GetData;
            }
        }

        Log::createEntry(Log::PERFORMANCE_LEVEL | Log::DEBUG_LEVEL, function () use ($request) {
            if ($request instanceof WebRequest) {
                return "Generating response for url " . $request->UrlPath;
            }

            if ($request instanceof CliRequest) {
                return "Starting CLI response";
            }

            return "";
        }, "ROUTER", $additionalData);

        Log::indent();

        $handlers = self::getAllUrlHandlers();

        // an empty-string Response to fall back on if nothing else is generated
        $response = new HtmlResponse();
        $response->setContent('');

        $filterResponse = true;

        try {
            // Iterate over each handler and ask them to generate a response.
            // If they do return a response we return that and exit the loop.
            // If they return false then we assume they couldn't handle the URL
            // and continue to the next handler.
            foreach ($handlers as $handler) {
                $generatedResponse = $handler->generateResponse($request);

                if ($generatedResponse !== false) {
                    Log::debug(function () use ($handler) {
                        return ["Handler `" . get_class($handler) . "` generated response.", []];
                    }, "ROUTER");

                    // it should be preferred for a handler to return a Response object,
                    // but checking this here retains the option for them to just return
                    // their output
                    if ($generatedResponse instanceof Response) {
                        $response = $generatedResponse;
                    } else {
                        $response->setContent($generatedResponse);
                    }

                    break;
                }
            }
        } catch (ForceResponseException $er) {
            // Clear any previous output in buffers to ensure we only send the forced response
            while (ob_get_level()) {
                ob_end_clean();
            }

            $response = $er->getResponse();
            $filterResponse = false;
        } catch (StopGeneratingResponseException $er) {
            $filterResponse = false;
        } catch (RhubarbException $er) {
            $response = ExceptionHandler::processException($er);
        } catch (\Exception $er) {
            $response = ExceptionHandler::processException(new NonRhubarbException($er));
        }

        if ($filterResponse) {
            Log::createEntry(Log::PERFORMANCE_LEVEL | Log::DEBUG_LEVEL, "Output filters started", "ROUTER");
            Log::indent();

            $filters = self::getAllResponseFilters();

            foreach ($filters as $filter) {
                $response = $filter->processResponse($response);
            }

            Log::createEntry(Log::PERFORMANCE_LEVEL | Log::DEBUG_LEVEL, "Output filters finished", "ROUTER");
            Log::outdent();
        }

        Log::performance("Response generated", "ROUTER");
        Log::outdent();

        return $response;
    }

    /**
     * Override to setup the module with the various classes this module supports.
     *
     * Code for module setup must occur here as during the constructor the auto load
     * mechanisms won't yet be in place.
     */
    protected function initialise()
    {

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
}
