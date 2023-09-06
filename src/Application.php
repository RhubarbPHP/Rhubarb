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

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Deployment\RelocationResourceDeploymentProvider;
use Rhubarb\Crown\Deployment\ResourceDeploymentProvider;
use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Exceptions\Handlers\DefaultExceptionHandler;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionSettings;
use Rhubarb\Crown\Exceptions\NonRhubarbException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Exceptions\StopGeneratingResponseException;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Request\CliRequest;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider;
use Rhubarb\Crown\Sessions\SessionProviders\SessionProvider;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class Application extends Module
{
    /**
     * True to enable developer only functionality
     *
     * @var bool
     */
    public $developerMode = false;

    /**
     * True to indicate this application is running on a live production server
     *
     * @var bool
     */
    public $live = false;

    /**
     * The currently processing request
     *
     * @var Request
     */
    private $activeRequest = null;

    /**
     * @var Module[]
     */
    private $modules = [];

    /**
     * @var UrlHandler[]
     */
    private $rootHandlers = [];

    /**
     * The active PHP context for the application
     *
     * @var PhpContext
     */
    private $phpContext = null;

    /**
     * The request currently being processed
     *
     * @var Request
     */
    private $request = null;

    /**
     * The dependency injection container for this instance
     *
     * @var Container
     */
    private $container = null;

    /**
     * True if the application is being unit tested.
     *
     * @see isUnitTesting()
     * @var bool
     */
    public $unitTesting = false;

    /**
     * The path to the root of the application source code.
     *
     * Usually set to APPLICATION_ROOT_DIR
     *
     * @var bool|string
     */
    public $applicationRootPath = "";

    /**
     * The path to a folder for temporary or unimportant files.
     *
     * Usually set to TEMP_DIR
     *
     * Should end in a trailing slash.
     *
     * @var string
     */
    public $tempPath = "";

    /**
     * The running application
     *
     * @see runningApplication()
     * @var Application
     */
    private static $currentApplication = null;

    /**
     * A collection of arrays provided by getSharedArray()
     *
     * @var array
     */
    private $sharedData = [];

    public final function __construct()
    {
        global $unitTesting;

        parent::__construct();

        $this->phpContext = new PhpContext();

        // $unitTesting is set in execute-test.php
        $this->unitTesting = (isset($unitTesting) && $unitTesting) ? true : false;
        $this->developerMode = false;
        $this->live = false;
        $this->applicationRootPath = APPLICATION_ROOT_DIR;
        $this->tempPath = TEMP_DIR;

        $this->setAsRunningApplication();

        ExceptionHandler::setProviderClassName(DefaultExceptionHandler::class);
        SessionProvider::setProviderClassName(PhpSessionProvider::class);
        ResourceDeploymentProvider::setProviderClassName(RelocationResourceDeploymentProvider::class);

        $this->registerModule($this);
    }

    /**
     * Sets the active request for the application.
     *
     * Normally this is handled transparently however unit tests may wish to inject a mock request object
     * here.
     *
     * @param Request $request
     */
    public function setCurrentRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets the dependency injection container
     *
     * @return Container
     */
    public final function container()
    {
        if (!$this->container){
            $this->container = new Container();
        }

        return $this->container;
    }

    /**
     * True if the application is being ran in a unit test harness.
     * @return bool
     */
    public final function isUnitTesting()
    {
        return $this->unitTesting;
    }

    /**
     * Get's the PHP Context
     *
     * @return PhpContext
     */
    public final function context()
    {
        return $this->phpContext;
    }

    /**
     * Register a module with the application.
     *
     * Also registers the dependencies of the application also
     *
     * @param Module $module
     */
    public final function registerModule(Module $module)
    {
        $dependencies = $module->getModules();

        foreach($dependencies as $dependency){
            $this->registerModule($dependency);
        }

        $this->modules[$module->getModuleName()] = $module;
    }

    /**
     * @return Module[]
     */
    public final function getRegisteredModules()
    {
        return array_values($this->modules);
    }

    /**
     * Get's an array of url handlers from all registered modules.
     *
     * @param UrlHandler[] $handlerSet
     * @return UrlHandler[]
     */
    public final function filterUrlHandlers($handlerSet)
    {
        $filteredHandlers = [];

        foreach ($handlerSet as $handlers) {
            $filteredHandlers = array_merge($filteredHandlers, $handlers);
        }

        uasort($filteredHandlers, function (UrlHandler $a, UrlHandler $b) {
            $aPriority = $a->getPriority();
            $bPriority = $b->getPriority();

            if ($aPriority == $bPriority) {
                return ($a->getCreationOrder() - $b->getCreationOrder());
            }

            return ($aPriority - $bPriority);
        });

        return $filteredHandlers;
    }

    /**
     * Asks all modules to initialise.
     *
     * Called automatically when processing requests.
     */
    public final function initialiseModules()
    {
        foreach ($this->modules as $module) {
            $module->initialiseModule();
        }

        // Initialise the application 'module' itself.
        $this->initialiseModule();

        $handlers = [];
        foreach ($this->modules as $module) {
            $handlers[] = $module->getUrlHandlers();
        }

        $this->rootHandlers = $this->filterUrlHandlers($handlers);
    }

    /**
     * Get's an array of response filters from all registered modules.
     *
     * @return \Rhubarb\Crown\ResponseFilters\ResponseFilter[]
     */
    private function getAllResponseFilters($preResponse = false)
    {
        $filters = [];

        foreach ($this->modules as $module) {

            $moduleFilters = $module->getResponseFilters();

            foreach($moduleFilters as $filter){
                if ($preResponse && $filter->isPreResponse()){
                    $filters[] = $filter;
                } elseif (!$preResponse && !$filter->isPreResponse()){
                    $filters[] = $filter;
                }
            }
        }

        return $filters;
    }

    /**
     * Generates the response content for the client.
     *
     * This is normally called by platform/execute-http.php and must be called after all
     * modules have been registered to guarantee the correct output.
     *
     * @static
     * @param Request $request
     * @return string|Response
     */
    public final function generateResponseForRequest(Request $request)
    {
        $this->setAsRunningApplication();
        $this->request = $request;
        $this->activeRequest = $request;

        $additionalData = [];
        if ($request instanceof WebRequest) {
            if (!empty($request->GetData)) {
                $additionalData = $request->GetData;
            }
        }

        Log::createEntry(Log::PERFORMANCE_LEVEL | Log::DEBUG_LEVEL, function () use ($request) {
            if ($request instanceof WebRequest) {
                return "Generating response for url " . $request->urlPath;
            }

            if ($request instanceof CliRequest) {
                return "Starting CLI response";
            }

            return "";
        }, "ROUTER", $additionalData);

        Log::indent();

        // an empty-string Response to fall back on if nothing else is generated
        $response = new HtmlResponse();
        $response->setContent('');

        try {

            // Run pre-response filters.
            $preFilters = $this->getAllResponseFilters(true);

            foreach($preFilters as $filter){
                $response = $filter->processResponse($response);
            }

            $filterResponse = true;

            // Iterate over each handler and ask them to generate a response.
            // If they do return a response we return that and exit the loop.
            // If they return false then we assume they couldn't handle the URL
            // and continue to the next handler.
            foreach ($this->rootHandlers as $handler) {
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
            $handler = $this->container()->getInstance(ExceptionHandler::class);
            $response = $handler->processException($er);
        } catch (\Exception $er) {
            $handler = $this->container()->getInstance(ExceptionHandler::class);
            $response = $handler->processException(new NonRhubarbException($er));
        }

        if ($filterResponse) {
            Log::createEntry(Log::PERFORMANCE_LEVEL | Log::DEBUG_LEVEL, "Output filters started", "ROUTER");
            Log::indent();

            $filters = $this->getAllResponseFilters();

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
     * Gets the current request derived from the PHP context.
     */
    public final function request()
    {
        if ($this->request == null){
            $this->request = $this->phpContext->createRequest();
        }

        return $this->request;
    }

    /**
     * Returns the currently active Application instance
     * @return Application
     */
    public static final function current()
    {
        // If we don't have an application it's possible we're running a custard command that
        // operates on the code base rather than the application itself. In these scenarios we
        // may not have an application defined. We create an empty one in this case so a DI
        // container will still be available.
        if (!self::$currentApplication){
            self::$currentApplication = new Application();
        }

        return self::$currentApplication;
    }

    protected function setAsRunningApplication()
    {
        Application::$currentApplication = $this;
    }

    /**
     * Returns a reference to an array stored using $key.
     *
     * Used to share data in this application instead of using statics.
     * @param $key
     * @return array
     */
    public final function &getSharedArray($key)
    {
        if (!isset($this->sharedData[$key])){
            $this->sharedData[$key] = [];
        }

        return $this->sharedData[$key];
    }

    /**
     * Obliterate a shared array.
     *
     * @param $key
     */
    public function clearSharedArray($key)
    {
        unset($this->sharedData[$key]);
    }
}
