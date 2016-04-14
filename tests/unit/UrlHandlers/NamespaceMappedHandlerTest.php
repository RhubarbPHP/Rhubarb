<?php

namespace Rhubarb\Crown\Tests\unit\UrlHandlers;

use Rhubarb\Crown\PhpContext;
use Rhubarb\Crown\HttpHeaders;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class NamespaceMappedHandlerTest extends RhubarbTestCase
{
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        $this->application->context()->simulateNonCli = true;
        $this->request = $this->application->request();
        $this->request->IsWebRequest = true;

        LayoutModule::disableLayout();
    }

    public function testHandlerFindsTestObject()
    {
        $this->request->urlPath = "/nmh/ObjectA/";

        $response = $this->application->generateResponseForRequest($this->request);
        $this->assertEquals("ObjectA Response", $response->getContent());

        $this->request->urlPath = "/nmh/SubFolder/ObjectB/";

        $response = $this->application->generateResponseForRequest($this->request);
        $this->assertEquals("ObjectB Response", $response->getContent());
    }

    public function testHandlerRedirectsWhenTrailingSlashMissing()
    {
        $this->request->urlPath = "/nmh/ObjectA";

        $response = $this->application->generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/ObjectA/", $headers["Location"]);
    }

    public function testHandlerRedirectsToIndexPage()
    {
        // This folder does contain an index so it should redirect.
        $this->request->urlPath = "/nmh/SubFolder/";

        $response = $this->application->generateResponseForRequest($this->request);

        $headers = $response->getHeaders();

        $this->assertEquals("/nmh/SubFolder/index/", $headers["Location"]);
    }
}
