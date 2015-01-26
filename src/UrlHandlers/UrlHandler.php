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
use Rhubarb\Crown\Exceptions\CoreException;
use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\IGeneratesResponse;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\RedirectResponse;

require_once __DIR__ . "/../IGeneratesResponse.class.php";

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
abstract class UrlHandler implements IGeneratesResponse
{
    /**
     * The URL stub which will allow this handler to consider a response
     *
     * @var string
     */
    protected $_url = "";

    /**
     * The URL which the handler has decided to process
     *
     * @var string
     */
    protected $_matchingUrl = "";

    /**
     * The priority of this handler against it's siblings.
     *
     * This only works presently on the top most handlers in the chain.
     *
     * @var int
     */
    private $_priority = 0;

    /**
     * If no priority is set the creation order is the next most important index
     *
     * @var int
     */
    private $_creationOrder = 0;

    /**
     * Giving a handler a name will let it replace a previous handler completely.
     *
     * Normally used by libraries to ensure handlers can be removed or replaced should a component of the library
     * not be required. The handler has to be registered at the top most level for this to work.
     *
     * @var string
     */
    private $_name = "";

    /**
     * A reference to this handlers parent handler
     *
     * @var UrlHandler
     */
    private $_parentHandler;

    /**
     * A counter to enable population of $_creationOrder
     *
     * @var int
     */
    private static $_creationOrderCount = 0;

    /**
     * A collection of url handlers that will be given a chance to process a response before this one (the parent).
     *
     * @var UrlHandler[]
     */
    protected $_childUrlHandlers = [];

    /**
     * Contains the portion of the URL up to and including the fragment that caused this handler to match.
     * @var string
     */
    protected $_handledUrl = "";

    /**
     * @var UrlHandler A reference to the currently executing URL Handler.
     */
    protected static $_executingUrlHandler;

    function __construct($childUrlHandlers = [])
    {
        self::$_creationOrderCount++;

        $this->_creationOrder = self::$_creationOrderCount;

        $this->AddChildUrlHandlers($childUrlHandlers);
    }

    public static function SetExecutingUrlHandler(UrlHandler $handler)
    {
        self::$_executingUrlHandler = $handler;
    }

    public static function GetExecutingUrlHandler()
    {
        return self::$_executingUrlHandler;
    }

    /**
     * Giving a handler a name will let it replace a previous handler completely.
     *
     * Normally used by libraries to ensure handlers can be removed or replaced should a component of the library
     * not be required. The handler has to be registered at the top most level for this to work.
     *
     * @param $name
     */
    public function SetName($name)
    {
        $this->_name = $name;
    }

    public function GetName()
    {
        return $this->_name;
    }

    /**
     * Sets the URL for the handler
     *
     * This is normally set by the AddUrlHandler method in Module. You should have no need to call it directly
     * except in unit tests.
     *
     * @param $url
     */
    public function SetUrl($url)
    {
        $this->_url = $url;
    }

    public function GetUrl()
    {
        return $this->_url;
    }

    public function GetParentHandler()
    {
        return $this->_parentHandler;
    }

    protected function SetParentHandler($parentHandler)
    {
        $this->_parentHandler = $parentHandler;
    }

    /**
     * Adds child url handlers to this the parent.
     *
     * @param UrlHandler[] $childHandlers
     * @return $this
     */
    private function AddChildUrlHandlers($childHandlers)
    {
        foreach ($childHandlers as $childUrl => $childHandler) {
            $childHandler->SetUrl($childUrl);
            $childHandler->SetParentHandler($this);

            $this->_childUrlHandlers[] = $childHandler;
        }

        return $this;
    }

    public function SetPriority($priority)
    {
        $this->_priority = $priority;
    }

    public function GetPriority()
    {
        return $this->_priority;
    }

    public function GetCreationOrder()
    {
        return $this->_creationOrder;
    }

    /**
     * Return the response if appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @return bool
     */
    protected abstract function GenerateResponseForRequest($request = null);

    /**
     * Takes a URL fragment understood by a child handler and adds back the parents URL fragment to form a complete URL.
     *
     * @param $childUrlFragment
     * @return string
     */
    protected function BuildCompleteChildUrl($childUrlFragment)
    {
        if ($this->_parentHandler !== null) {
            return $this->_parentHandler->_matchingUrl . $childUrlFragment;
        } else {
            return $childUrlFragment;
        }
    }

    protected function GetAbsoluteHandledUrl()
    {
        $request = Context::CurrentRequest();

        return $request->Server("REQUEST_SCHEME") . "://" . $request->Server("SERVER_NAME") . $this->_handledUrl;
    }

    /**
     * Return the response when appropriate or false if no response could be generated.
     *
     * If child handlers are present they are given priority.
     *
     * @param mixed $request
     * @param bool|string $currentUrlFragment
     * @return bool
     */
    public function GenerateResponse($request = null, $currentUrlFragment = false)
    {
        if ($currentUrlFragment === false) {
            $currentUrlFragment = $request->UrlPath;
        }

        if (!$this->MatchesRequest($request, $currentUrlFragment)) {
            return false;
        }

        $context = new Context();
        $context->UrlHandler = $this;

        $this->_matchingUrl = $this->GetMatchingUrlFragment($request, $currentUrlFragment);

        if ($this->_parentHandler) {
            $this->_handledUrl = $this->_parentHandler->_handledUrl . $this->_matchingUrl;
        } else {
            $this->_handledUrl = $this->_matchingUrl;
        }

        $childUrlFragment = substr($currentUrlFragment, strlen($this->_matchingUrl));

        foreach ($this->_childUrlHandlers as $childHandler) {
            $response = $childHandler->GenerateResponse($request, $childUrlFragment);

            if ($response !== false) {
                return $response;
            }
        }

        UrlHandler::SetExecutingUrlHandler($this);

        Log::Debug(function () {
            return "Handler " . get_class($this) . " selected to generate response";
        }, "ROUTER");

        Log::Indent();

        $response = $this->GenerateResponseForRequest($request, $currentUrlFragment);

        Log::Debug(function () use ($response) {
            if ($response !== false) {
                return "Response generated by handler";
            }

            return "Handler deferred generation";
        }, "ROUTER");

        Log::Outdent();

        return $response;
    }

    public function GenerateResponseForException(CoreException $er)
    {
        $response = new HtmlResponse();
        $response->SetContent($er->GetPublicMessage());

        return $response;
    }

    /**
     * Returns the portion of the URL that this handler would be able to return a response for.
     *
     * @param \Rhubarb\Crown\Request\Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function GetMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        if ($currentUrlFragment[strlen($currentUrlFragment) - 1] != "/") {
            $currentUrlFragment .= "/";
        }

        // Some URL Handlers don't have a url at all in which case we assume they apply
        // before even considering the url.
        if ($this->_url == "") {
            return "/";
        }

        if (stripos($currentUrlFragment, $this->_url) === 0) {
            return $this->_url;
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
    private function MatchesRequest(Request $request, $currentUrlFragment = "")
    {
        // Some URL Handlers don't have a url at all in which case we assume they apply
        // before even considering the url.
        if ($this->_url == "") {
            return true;
        }

        return (stripos($currentUrlFragment, $this->_url) === 0);
    }

    /**
     * Force a redirect response
     *
     * @param $url The URL to redirect to.
     * @throws \Rhubarb\Crown\Exceptions\ForceResponseException
     */
    public static function RedirectToUrl($url)
    {
        throw new ForceResponseException(new RedirectResponse($url));
    }
}
