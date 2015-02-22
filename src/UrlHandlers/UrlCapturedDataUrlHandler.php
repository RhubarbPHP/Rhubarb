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

use Rhubarb\Crown\Request\Request;

class UrlCapturedDataUrlHandler extends ClassMappedUrlHandler
{
    private $capturedData = null;

    public function generateResponseForRequest($request = null)
    {
        $object = $this->createHandlingClass();

        if (method_exists($object, "SetUrlCapturedData")) {
            call_user_func(array($object, "SetUrlCapturedData"), $this->capturedData);
        }

        return $object->generateResponse($request);
    }

    /**
     * Should be implemented to return a true or false as to whether this handler supports the given request.
     *
     * Normally this involves testing the request URI.
     *
     * @param Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        $uri = $currentUrlFragment;

        if (preg_match("|^" . rtrim($this->url, "/") . "/([^/]+)/|", $uri, $match)) {
            $this->capturedData = $match[1];
            return $match[0];
        }

        return false;
    }
}