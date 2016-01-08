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

namespace Rhubarb\Crown\UrlHandlers;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\GeneratesResponse;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Response\Response;

require_once __DIR__ . "/../Response/GeneratesResponse.php";

/**
 * The base class for URL Handlers.
 *
 * Once registered a URL handler will get an opportunity to generate a response to a given URL
 * It should override UrlHandler and return the response when appropriate or false if no response
 * could be generated.
 *
 * We return false rather than an exception for performance reasons.
 *
 */
abstract class UrlHandler implements GeneratesResponse
{
    /**
     * The URL stub which will allow this handler to consider a response
     *
     * @var string
     */
    protected $url = "";

    /**
     * The URL which the handler has decided to process
     *
     * @var string
     */
    protected $matchingUrl = "";

    /**
     * The priority of this handler against it's siblings.
     *
     * This only works presently on the top most handlers in the chain.
     *
     * @var int
     */
    private $priority = 0;

    /**
     * If no priority is set the creation order is the next most important index
     *
     * @var int
     */
    private $creationOrder = 0;

    /**
     * Giving a handler a name will let it replace a previous handler completely.
     *
     * Normally used by libraries to ensure handlers can be removed or replaced should a component of the library
     * not be required. The handler has to be registered at the top most level for this to work.
     *
     * @var string
     */
    private $name = "";

    /**
     * A reference to this handlers parent handler
     *
     * @var UrlHandler
     */
    private $parentHandler;

    /**
     * A counter to enable population of $_creationOrder
     *
     * @var int
     */
    private static $creationOrderCount = 0;

    /**
     * A collection of url handlers that will be given a chance to process a response before this one (the parent).
     *
     * @var UrlHandler[]
     */
    protected $childUrlHandlers = [];

    /**
     * Contains the portion of the URL up to and including the fragment that caused this handler to match.
     * @var string
     */
    protected $handledUrl = "";

    /**
     * @var UrlHandler A reference to the currently executing URL Handler.
     */
    protected static $executingUrlHandler;

    function __construct($childUrlHandlers = [])
    {
        self::$creationOrderCount++;

        $this->creationOrder = self::$creationOrderCount;

        $this->addChildUrlHandlers($childUrlHandlers);

        $this->url = $this->getDefaultUrl();
    }

    public static function setExecutingUrlHandler(UrlHandler $handler)
    {
        self::$executingUrlHandler = $handler;
    }

    public static function getExecutingUrlHandler()
    {
        return self::$executingUrlHandler;
    }

    /**
     * Giving a handler a name will let it replace a previous handler completely.
     *
     * Normally used by libraries to ensure handlers can be removed or replaced should a component of the library
     * not be required. The handler has to be registered at the top most level for this to work.
     *
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the URL for the handler
     *
     * This is normally set by the AddUrlHandler method in Module. You should have no need to call it directly
     * except in unit tests.
     *
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Returns the default URL fragment for this handler.
     *
     * This is seldom overriden - in most cases it is better to simply call setUrl() when
     * configuring the handler or use the key/value pair syntax when registering the handler.
     */
    protected function getDefaultUrl()
    {
        return "";
    }

    public function getUrl()
    {
        $parentUrl = ( $this->parentHandler ) ? $this->parentHandler->matchingUrl : "";

        return $parentUrl.$this->url;
    }

    public function getParentHandler()
    {
        return $this->parentHandler;
    }

    protected function setParentHandler($parentHandler)
    {
        $this->parentHandler = $parentHandler;
    }

    /**
     * Adds child url handlers to this the parent.
     *
     * @param UrlHandler[] $childHandlers
     * @return $this
     */
    private function addChildUrlHandlers($childHandlers)
    {
        foreach ($childHandlers as $childUrl => $childHandler) {
            $childHandler->setUrl($childUrl);
            $childHandler->setParentHandler($this);

            $this->childUrlHandlers[] = $childHandler;
        }

        return $this;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getCreationOrder()
    {
        return $this->creationOrder;
    }

    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool|Response
     */
    abstract protected function generateResponseForRequest($request = null);

    /**
     * Takes a URL fragment understood by a child handler and adds back the parents URL fragment to form a complete URL.
     *
     * @param $childUrlFragment
     * @return string
     */
    protected function buildCompleteChildUrl($childUrlFragment)
    {
        if ($this->parentHandler !== null) {
            return $this->parentHandler->matchingUrl . $childUrlFragment;
        } else {
            return $childUrlFragment;
        }
    }

    protected function getAbsoluteHandledUrl()
    {
        $request = Context::currentRequest();

        return $request->Server("REQUEST_SCHEME") . "://" . $request->Server("SERVER_NAME") . $this->handledUrl;
    }

    /**
     * Return the response when appropriate or false if no response could be generated.
     *
     * If child handlers are present they are given priority.
     *
     * @param mixed $request
     * @param bool|string $currentUrlFragment
     * @return bool|Response
     */
    public function generateResponse($request = null, $currentUrlFragment = false)
    {
        if ($currentUrlFragment === false) {
            $currentUrlFragment = $request->UrlPath;
        }

        if (!$this->matchesRequest($request, $currentUrlFragment)) {
            return false;
        }

        UrlHandler::setExecutingUrlHandler($this);

        Log::debug(function () {
            return "Handler " . get_class($this) . " selected to generate response";
        }, "ROUTER");

        Log::indent();

        $context = new Context();
        $context->UrlHandler = $this;

        $this->matchingUrl = $this->getMatchingUrlFragment($request, $currentUrlFragment);

        if ($this->parentHandler) {
            $this->handledUrl = $this->parentHandler->handledUrl . $this->matchingUrl;
        } else {
            $this->handledUrl = $this->matchingUrl;
        }

        $childUrlFragment = substr($currentUrlFragment, strlen($this->matchingUrl));

        foreach ($this->childUrlHandlers as $childHandler) {
            $response = $childHandler->generateResponse($request, $childUrlFragment);

            if ($response !== false) {
                return $response;
            }
        }

        $response = $this->generateResponseForRequest($request, $currentUrlFragment);

        Log::debug(function () use ($response) {
            if ($response !== false) {
                return "Response generated by handler";
            }

            return "Handler deferred generation";
        }, "ROUTER");

        Log::outdent();

        return $response;
    }

    public function generateResponseForException(RhubarbException $er)
    {
        $response = new HtmlResponse();
        $response->setContent($er->getPublicMessage());

        return $response;
    }

    /**
     * Returns the portion of the URL that this handler would be able to return a response for.
     *
     * @param \Rhubarb\Crown\Request\Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        if ($currentUrlFragment[strlen($currentUrlFragment) - 1] != "/") {
            $currentUrlFragment .= "/";
        }

        // Some URL Handlers don't have a url at all in which case we assume they apply
        // before even considering the url.
        if ($this->url == "") {
            return "/";
        }

        if (stripos($currentUrlFragment, $this->url) === 0) {
            return $this->url;
        }

        return false;
    }

    /**
     * Returns true of this handler's URL allows it to consider a response for the request.
     *
     * In general terms this means that the current URL begins with the URL this handler is registered to handle.
     * However more advanced handlers might use other aspects of the request instead of the URL to determine this.
     *
     * @param Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    private function matchesRequest(Request $request, $currentUrlFragment = "")
    {
        // Some URL Handlers don't have a url at all in which case we assume they apply
        // before even considering the url.
        if ($this->url == "") {
            return true;
        }

        return (stripos($currentUrlFragment, $this->url) === 0);
    }

    /**
     * Force a redirect response
     *
     * @param $url string The URL to redirect to.
     * @throws \Rhubarb\Crown\Exceptions\ForceResponseException
     */
    public static function redirectToUrl($url)
    {
        throw new ForceResponseException(new RedirectResponse($url));
    }
}
