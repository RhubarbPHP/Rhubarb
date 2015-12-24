<?php

namespace Rhubarb\Crown\Tests\unit\UrlHandlers;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\HttpHeaders;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class NamespaceMappedHandlerTest extends RhubarbTestCase
{
    protected $request = null;

    protected function setUp()
    {
        $this->request = Context::CurrentRequest();
        $this->request->IsWebRequest = true;

        LayoutModule::disableLayout();
    }

    public function testHandlerFindsTestObject()
    {
        $this->request->UrlPath = "/nmh/ObjectA/";

        $response = Module::generateResponseForRequest($this->request);
        $this->assertEquals("ObjectA Response", $response->getContent());

        $this->request->UrlPath = "/nmh/SubFolder/ObjectB/";

        $response = Module::generateResponseForRequest($this->request);
        $this->assertEquals("ObjectB Response", $response->getContent());
    }

    public function testHandlerRedirectsWhenTrailingSlashMissing()
    {
        $this->request->UrlPath = "/nmh/ObjectA";

        $response = Module::generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/ObjectA/", $headers["Location"]);
    }

    public function testHandlerRedirectsToIndexPage()
    {
        HttpHeaders::clearHeaders();

        // This folder does contain an index so it should redirect.
        $this->request->UrlPath = "/nmh/SubFolder/";

        $response = Module::generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/SubFolder/index/", $headers["Location"]);
    }
}
