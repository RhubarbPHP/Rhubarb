<?php

namespace Rhubarb\Crown\Tests\unit\UrlHandlers;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\GeneratesResponse;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;

class ClassMappedHandlerTest extends RhubarbTestCase
{
    public function testUrlHandled()
    {
        $request = new WebRequest();
        $request->urlPath = "/wrong/path/";

        $handler = new ClassMappedUrlHandler(TestTarget::class);
        $handler->setUrl("/right/path/");

        $response = $handler->generateResponse($request);

        $this->assertFalse($response);

        $request = new WebRequest();
        $request->urlPath = "/right/path/";

        $response = $handler->generateResponse($request);

        $this->assertEquals("bing bang bong", $response->getContent());
    }
}

class TestTarget implements GeneratesResponse
{
    public function generateResponse($request = null)
    {
        $response = new Response();
        $response->setContent("bing bang bong");

        return $response;
    }
}