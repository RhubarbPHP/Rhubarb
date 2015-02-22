<?php

namespace Rhubarb\Crown\Tests\UrlHandlers;

use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class UrlHandlerTest extends RhubarbTestCase
{
    public function testUrlPriorities()
    {
        // Our test case has setup a handler which should come before the validate login handlers.
        $request = new WebRequest();
        $request->UrlPath = "/priority-test/simple/";

        $response = Module::generateResponseForRequest($request);

        $this->assertNotInstanceOf("Rhubarb\Crown\Response\RedirectResponse", $response);
    }

    public function testChildHandler()
    {
        $child = new ClassMappedUrlHandler("Rhubarb\Crown\Tests\UrlHandlers\Fixtures\NamespaceMappedHandlerTests\SubFolder\ObjectB");
        $parent = new ClassMappedUrlHandler("Rhubarb\Crown\Tests\UrlHandlers\Fixtures\NamespaceMappedHandlerTests\ObjectA",
            ["child/" => $child]);
        $parent->setUrl("/parent/");

        $request = new WebRequest();
        $request->UrlPath = "/parent/child/";

        $response = $parent->generateResponse($request);

        $this->assertEquals("ObjectB Response", $response);

        $request->UrlPath = "/parent/not-child/";

        $response = $parent->generateResponse($request);

        $this->assertEquals("ObjectA Response", $response);

        $request->UrlPath = "/not-parent/not-child/";

        $response = $parent->generateResponse($request);

        $this->assertFalse($response);
    }
}

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
