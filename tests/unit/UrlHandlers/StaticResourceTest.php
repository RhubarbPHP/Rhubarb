<?php

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
        $this->request = PhpContext::currentRequest();
        $this->request->IsWebRequest = true;
    }

    public function tearDown()
    {
        $this->request = null;
    }

    public function testStaticFileReturned()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/test.txt");
        $handler->setUrl("/test.txt");

        $this->request->UrlPath = "/";

        $response = $handler->generateResponse($this->request);

        $this->assertFalse($response);

        $this->request->UrlPath = "/test.txt";
        $response = $handler->generateResponse($this->request);

        $this->assertEquals("This is a static resource", $response->getContent());
    }

    public function testStaticFileDisablesLayout()
    {
        LayoutModule::enableLayout();

        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/test.txt");
        $handler->setUrl("/test.txt");
        $this->request->UrlPath = "/test.txt";

        $handler->generateResponse($this->request);

        $this->assertTrue(LayoutModule::isDisabled());
    }

    public function testStaticFolderFindsAndReturnsFiles()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/");
        $handler->setUrl("/files/");
        $this->request->UrlPath = "/files/test2.txt";

        $response = $handler->generateResponse($this->request);
        $this->assertEquals("This is another static resource", $response->getContent());

        $this->request->UrlPath = "/files/subfolder/test3.txt";
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

        $this->request->UrlPath = "/files/non-extant.txt";

        $handler->generateResponse($this->request);
    }

    public function testMimeTypeSetCorrectly()
    {
        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../Fixtures/UrlHandlers/");
        $handler->setUrl("/files/");

        $this->request->UrlPath = "/files/test.txt";
        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertArrayHasKey("Content-Type", $headers);
        $this->assertEquals("text/plain; charset=us-ascii", $headers["Content-Type"]);

        $this->request->UrlPath = "/files/base.css";
        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertArrayHasKey("Content-Type", $headers);
        $this->assertEquals("text/css", $headers["Content-Type"]);

        $handler = new StaticResourceUrlHandler(__DIR__ . "/../../../resources/resource-manager.js");
        $handler->setUrl("/js/resource-manager.js");

        $this->request->UrlPath = "/js/resource-manager.js";

        $response = $handler->generateResponse($this->request);
        $headers = $response->getHeaders();

        $this->assertEquals("application/javascript; charset=us-ascii", $headers["Content-Type"]);
    }
}
