<?php

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
        $request->UrlPath = "/priority-test/simple/";

        $response = Module::generateResponseForRequest($request);

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

    public function testUrlExtractedFromHandler()
    {
        LayoutModule::enableLayout();

        $request = new WebRequest();
        $request->UrlPath = "/computed-url/test/";

        $response = Module::generateResponseForRequest($request);

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
