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

namespace Rhubarb\Crown\UrlHandlers;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\GeneratesResponseInterface;

require_once __DIR__ . "/UrlHandler.php";

/**
 * This very simple handler scans a namespace for a matching object or sub handler.
 *
 * The handler explodes the url and explores it folder at a time (to allow for passing
 * control to another handler)
 */
class NamespaceMappedUrlHandler extends UrlHandler
{
    /**
     * True if this handler is processing as a consequence of a previous handler not finding any target.
     *
     * This is required to stop infinite nesting if this handler also fails to find a target.
     *
     * @var bool
     */
    static $onTargetNotFoundExecuting = false;

    private $namespace = "";

    /**
     * @param string $namespace The corresponding namespace prefix to use. This should have no leading or trailing slash.
     * @param array $children
     */
    public function __construct($namespace = "Site", $children = [])
    {
        parent::__construct($children);

        $this->namespace = trim($namespace, "\\");
    }

    /**
     * Returns the name of the class that should handle the given page url.
     *
     * @param $pageUrl string The last part of the url.
     * @return mixed
     */
    protected function convertUrlToClassName($pageUrl)
    {
        return str_replace(" ", "", ucwords(str_replace("-", " ", $pageUrl)));
    }

    protected function generateResponseForRequest($request = null, $currentUrlFragment = "")
    {
        if ($request !== null && $request instanceof WebRequest) {
            $url = $currentUrlFragment;
        } else {
            $url = null;
        }

        if (stripos($url, $this->url) !== 0) {
            return false;
        }

        $relevantUrl = preg_replace("|^" . $this->url . "|", "", $url);

        // Make sure the url we consider ends in a slash. Later we'll redirect the user
        // to this URL if we find a target (to make sure relative urls work properly in all
        // cases).
        $redirectTo = false;

        if (strlen($relevantUrl) > 0) {
            if ($relevantUrl[strlen($relevantUrl) - 1] != "/") {
                $redirectTo = $url . "/";
                $relevantUrl .= "/";
            }
        }

        $urlParts = explode("/", $relevantUrl);
        $folderUrl = "/";

        foreach ($urlParts as $part) {
            // Sanitise the last part of the url to translate more URL like names to more class like names
            $classPart = $this->convertUrlToClassName($part);

            $objectUrl = $folderUrl . $classPart;

            $objectClass = "\\" . $this->namespace . str_replace("/", "\\", $objectUrl);

            if (class_exists($objectClass)) {
                if ($redirectTo !== false) {
                    UrlHandler::redirectToUrl($this->buildCompleteChildUrl($redirectTo));
                }

                $object = new $objectClass();

                if (is_a($object, "\Rhubarb\Crown\Response\GeneratesResponseInterface")) {
                    return $this->onTargetFound($object, $request);
                }
            }

            $folderUrl .= $part . "/";
        }

        return $this->onNoTargetFound($request, $currentUrlFragment);
    }

    /**
     * Handles the event that a matching target is found.
     *
     * Normally this just asks the $object to generate a response for the request
     *
     * @param GeneratesResponseInterface $object
     * @param $request
     * @return mixed
     */
    protected function onTargetFound(GeneratesResponseInterface $object, $request)
    {
        return $object->generateResponse($request);
    }

    /**
     * Override this to provide a default response in the event that no action could be found.
     *
     * @param \Rhubarb\Crown\Request\Request $request The original request we tried to find a target for.
     * @param $currentUrlFragment
     * @return bool|string
     */
    protected function onNoTargetFound($request, $currentUrlFragment = "")
    {
        if (self::$onTargetNotFoundExecuting) {
            return false;
        }

        self::$onTargetNotFoundExecuting = true;

        // Try generating a response for the url with /index on the end.
        // Note that we don't try /index/. This is very deliberate as we want the default behaviour
        // of the handler to kick in and redirect us to /index/ rather than just outputting it's
        // contents.
        if ($currentUrlFragment[strlen($currentUrlFragment) - 1] != "/") {
            $currentUrlFragment .= "/";
        }

        $currentUrlFragment .= "index";

        $response = $this->generateResponseForRequest($request, $currentUrlFragment);

        self::$onTargetNotFoundExecuting = false;

        return $response;
    }
}
