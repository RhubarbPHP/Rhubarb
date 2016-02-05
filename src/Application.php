<?php

namespace Rhubarb\Crown;

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

final class Application
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

    public $applicationRootPath = "";

    /**
     * The running application
     *
     * @see runningApplication()
     * @var Application
     */
    private static $currentApplication = null;

    public final function __construct()
    {
        global $unitTesting;

        $this->phpContext = new PhpContext();

        // $unitTesting is set in execute-test.php
        $this->unitTesting = (isset($unitTesting) && $unitTesting) ? true : false;
        $this->developerMode = false;
        $this->live = false;
        $this->applicationRootPath = realpath(VENDOR_DIR."/../");

        $this->container()->registerClass(ExceptionHandler::class, DefaultExceptionHandler::class, true);
        $this->container()->registerClass(ExceptionSettings::class, ExceptionSettings::class, true);
        $this->container()->registerClass(SessionProvider::class, PhpSessionProvider::class, true);

        $this->setAsRunningApplication();
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
    public function isUnitTesting()
    {
        return $this->unitTesting;
    }

    /**
     * Get's the PHP Context
     *
     * @return PhpContext
     */
    public function getPhpContext()
    {
        return $this->phpContext;
    }

    /**
     * Register a module with the application.
     *
     * Registers the child modules returned by the
     *
     * @param Module $module
     */
    public function registerModule(Module $module)
    {
        $dependencies = $module->getModules();

        foreach($dependencies as $dependency){
            $this->registerModule($dependency);
        }

        $this->modules[$module->getModuleName()] = $module;
    }

    public function getModules()
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
                return ($a->getCreationOrder() > $b->getCreationOrder());
            }

            return ($aPriority <= $bPriority);
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
    private function getResponseFilters()
    {
        $filters = [];

        foreach ($this->modules as $module) {
            $filters = array_merge($filters, $module->getResponseFilters());
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
    public function generateResponseForRequest(Request $request)
    {
        $this->setAsRunningApplication();
        $this->request = $request;

        $this->initialiseModules();

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
        $response->SetContent('');

        $filterResponse = true;

        try {
            // Iterate over each handler and ask them to generate a response.
            // If they do return a response we return that and exit the loop.
            // If they return false then we assume they couldn't handle the URL
            // and continue to the next handler.
            foreach ($this->rootHandlers as $handler) {
                $generatedResponse = $handler->generateResponse($request);

                if ($generatedResponse !== false) {
                    Log::Debug(function () use ($handler) {
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

            $filters = $this->getResponseFilters();

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
    public function currentRequest()
    {
        if ($this->request == null){
            $this->request = $this->phpContext->createRequest();
        }

        return $this->request;
    }

    public static function current()
    {
        return self::$currentApplication;
    }

    protected function setAsRunningApplication()
    {
        Application::$currentApplication = $this;
    }
}