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

namespace Rhubarb\Crown\Tests\unit\UrlHandlers;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests\ObjectA;
use Rhubarb\Crown\Tests\Fixtures\UrlHandlers\NamespaceMappedHandlerTests\SubFolder\ObjectB;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class UrlHandlerTest extends RhubarbTestCase
{
    public function testUrlPriorities()
    {
        // Our test case has setup a handler which should come before the validate login handlers.
        $request = new WebRequest();
        $request->urlPath = "/priority-test/simple/";

        $response = $this->application->generateResponseForRequest($request);

        $this->assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testChildHandler()
    {
        $child = new ClassMappedUrlHandler(ObjectB::class);
        $parent = new ClassMappedUrlHandler(
            ObjectA::class,
            ["child/" => $child]
        );
        $parent->setUrl("/parent/");

        $request = new WebRequest();
        $request->urlPath = "/parent/child/";

        $response = $parent->generateResponse($request);

        $this->assertEquals("ObjectB Response", $response);

        $request->urlPath = "/parent/not-child/";

        $response = $parent->generateResponse($request);

        $this->assertEquals("ObjectA Response", $response);

        $request->urlPath = "/not-parent/not-child/";

        $response = $parent->generateResponse($request);

        $this->assertFalse($response);
    }

    public function testUrlExtractedFromHandler()
    {
        LayoutModule::enableLayout();

        $request = new WebRequest();
        $request->urlPath = "/computed-url/test/";

        $response = $this->application->generateResponseForRequest($request);

        $this->assertEquals("TopComputed URL ResponseTail", $response->getContent());
    }
}


class TestChildHandler extends UrlHandler
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
        $response->setContent("child");

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
