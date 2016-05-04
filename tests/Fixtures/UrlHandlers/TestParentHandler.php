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

namespace Rhubarb\Crown\Tests\Fixtures\UrlHandlers;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class TestParentHandler extends UrlHandler
{
    public $stub = "/";

    /**
     * Return the response when appropriate or false if no response could be generated.
     *
     * @param mixed $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function generateResponseForRequest($request = null, $currentUrlFragment = "")
    {
        $response = new HtmlResponse();
        $response->setContent("parent");

        return $response;
    }

    /**
     * Should be implemented to return a true or false as to whether this handler supports the given request.
     *
     * Normally this involves testing the request URI.
     *
     * @param \Rhubarb\Crown\Request\Request $request
     * @param string $currentUrlFragment
     * @return bool
     */
    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        return (stripos($currentUrlFragment, $this->stub) === 0);
    }
}