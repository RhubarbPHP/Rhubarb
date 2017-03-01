<?php
/**
 * Copyright (c) 2017 RhubarbPHP.
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

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\GeneratesResponseInterface;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\CallableUrlHandler;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\GreedyUrlHandler;
use Rhubarb\Crown\UrlHandlers\NumericGreedyUrlHandler;

class NumericGreedyUrlHandlerTest extends RhubarbTestCase
{
    public function testUrlHandled()
    {
        $request = new WebRequest();
        $request->urlPath = "/right/path/next/";

        $handler = new NumericGreedyUrlHandler(function($arg1){
            return new TestTargetForNumericGreedy($arg1);
        });

        $handler->setUrl("/right/path/");

        $response = $handler->generateResponse($request);
        $this->assertFalse($response);

        $request->urlPath = "/right/path/1234/";
        $response = $handler->generateResponse($request);
        $this->assertEquals("1234", $response->getContent());

        $handler = new NumericGreedyUrlHandler(function($arg1){
            return new TestTargetForNumericGreedy($arg1);
        }, true, []);

        $request->urlPath = "/right/path/";

        $response = $handler->generateResponse($request);
        $this->assertEquals("", $response->getContent());
    }
}

class TestTargetForNumericGreedy implements GeneratesResponseInterface
{
    /**
     * @var
     */
    private $arg1;
    /**
     * @var
     */
    private $arg2;

    public function __construct($arg1, $arg2 = null)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function generateResponse($request = null)
    {
        $response = new Response();
        $response->setContent($this->arg1.$this->arg2);

        return $response;
    }
}