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

use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\Exceptions\StaticResource404Exception;
use Rhubarb\Crown\Exceptions\StaticResourceNotFoundException;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\StaticResourceUrlHandler;

class StaticResourceTest extends RhubarbTestCase
{
    protected $request = null;

    public function setUp()
    {
        parent::setUp();

        $this->request = $this->application->request();
        $this->request->IsWebRequest = true;
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->request = null;
    }

    public function testStaticFileReturned()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/test.txt");
        $handler->setUrl("/test.txt");

        $this->request->urlPath = "/";

        $response = $handler->generateResponse($this->request);

        $this->assertFalse($response);

        $this->request->urlPath = "/test.txt";
        $response = $handler->generateResponse($this->request);

        $this->assertEquals("This is a static resource", $response->getContent());
    }

    public function testStaticFileDisablesLayout()
    {
        LayoutModule::enableLayout();

        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/test.txt");
        $handler->setUrl("/test.txt");
        $this->request->urlPath = "/test.txt";

        $handler->generateResponse($this->request);

        $this->assertTrue(LayoutModule::isDisabled());
    }

    public function testStaticFolderFindsAndReturnsFiles()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/");
        $handler->setUrl("/files/");
        $this->request->urlPath = "/files/test2.txt";

        $response = $handler->generateResponse($this->request);
        $this->assertEquals("This is another static resource", $response->getContent());

        $this->request->urlPath = "/files/subfolder/test3.txt";
        $response = $handler->generateResponse($this->request);
        $this->assertEquals("test3", $response->getContent());
    }

    public function testExceptionThrownIfPathDoesntExist()
    {
        $this->setExpectedException(StaticResourceNotFoundException::class);

        new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/non-extant-file.txt");
    }

    public function testExceptionThrownIfFileNotFoundInDirectory()
    {
        $this->setExpectedException(StaticResource404Exception::class);

        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/");
        $handler->setUrl("/files/");

        $this->request->urlPath = "/files/non-extant.txt";

        $handler->generateResponse($this->request);
    }

    public function testMimeTypeSetCorrectly()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/");
        $handler->setUrl("/files/");

        $this->request->urlPath = "/files/test.txt";
        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertArrayHasKey("Content-Type", $headers);
        $this->assertEquals("text/plain; charset=us-ascii", $headers["Content-Type"]);

        $this->request->urlPath = "/files/base.css";
        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertArrayHasKey("Content-Type", $headers);
        $this->assertEquals("text/css", $headers["Content-Type"]);

        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../../resources/resource-manager.js");
        $handler->setUrl("/js/resource-manager.js");

        $this->request->urlPath = "/js/resource-manager.js";

        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertEquals("application/javascript; charset=us-ascii", $headers["Content-Type"]);
    }
}
