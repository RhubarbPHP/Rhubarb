<?php

namespace Rhubarb\Crown\Tests\Layout;

use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\JsonResponse;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class LayoutModuleTest extends RhubarbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        LayoutModule::enableLayout();
    }

    public function testLayoutPathIsRemembered()
    {
        new LayoutModule("Rhubarb\Crown\Tests\Layout\LayoutTest2");

        $this->assertEquals(
            "Rhubarb\Crown\Tests\Layout\LayoutTest2",
            LayoutModule::getLayoutClassName());
    }

    public function testAjaxRequestDisablesLayout()
    {
        LayoutModule::enableLayout();

        new LayoutModule("Rhubarb\Crown\Tests\Layout\LayoutTest2");

        // Normal request
        $this->assertFalse(LayoutModule::isDisabled());

        $_SERVER["HTTP_X_REQUESTED_WITH"] = "some-odd-request";

        new LayoutModule("Rhubarb\Crown\Tests\Layout\LayoutTest2");

        // Some odd request
        $this->assertFalse(LayoutModule::isDisabled());

        $_SERVER["HTTP_X_REQUESTED_WITH"] = "XMLHttpRequest";

        new LayoutModule("Rhubarb\Crown\Tests\Layout\LayoutTest2");

        // Ajax request
        $this->assertTrue(LayoutModule::isDisabled());

        unset($_SERVER["HTTP_X_REQUESTED_WITH"]);

        LayoutModule::enableLayout();
    }

    public function testLayoutCanBeDisabled()
    {
        LayoutModule::disableLayout();

        $this->assertTrue(LayoutModule::isDisabled());

        LayoutModule::enableLayout();

        $this->assertFalse(LayoutModule::isDisabled());
    }

    public function testLayoutCanBeChanged()
    {
        new LayoutModule("Rhubarb\Crown\Tests\Layout\TestLayout");
        LayoutModule::setLayoutClassName("Rhubarb\Crown\Tests\Layout\LayoutTest2");

        $this->assertEquals("Rhubarb\Crown\Tests\Layout\LayoutTest2", LayoutModule::getLayoutClassName());
    }

    public function testLayoutDoesntWorkForJsonResponse()
    {
        LayoutModule::setLayoutClassName("Rhubarb\Crown\Tests\Layout\TestLayout");

        $model = new \stdClass();
        $model->Field = "Value";

        $response = new JsonResponse();
        $response->setContent($model);

        $layoutFilter = new LayoutFilter();
        $layoutFilter->processResponse($response);

        $this->assertEquals("Value", $response->getContent()->Field);
    }

    public function testLayoutWorks()
    {
        LayoutModule::setLayoutClassName("Rhubarb\Crown\Tests\Layout\TestLayout");

        $request = new WebRequest();
        $request->UrlPath = "/simple/";
        $request->IsWebRequest = true;

        $response = Module::generateResponseForRequest($request);

        $this->assertEquals("TopDon't change this content - it should match the unit test.Tail",
            $response->getContent());
    }

    public function testLayoutFilterThrowsException()
    {
        LayoutModule::setLayoutClassName("Rhubarb\Crown\Tests\Layout\NonExistant");

        $request = new WebRequest();
        $request->UrlPath = "/simple/";
        $request->IsWebRequest = true;

        $this->setExpectedException("Rhubarb\Crown\Layout\Exceptions\LayoutNotFoundException");

        ExceptionHandler::disableExceptionTrapping();

        Module::generateResponseForRequest($request);
    }

    public function testLayoutCanBeAnonymousFunction()
    {
        LayoutModule::setLayoutClassName(function () {
            return "Rhubarb\Crown\Tests\Layout\TestLayout";
        });

        $request = new WebRequest();
        $request->UrlPath = "/simple/";
        $request->IsWebRequest = true;

        $response = Module::generateResponseForRequest($request);

        $this->assertEquals("TopDon't change this content - it should match the unit test.Tail",
            $response->GetContent());
    }

    public function testHeadItems()
    {
        // Reenable this as it was disabled in the previous test.
        ExceptionHandler::enableExceptionTrapping();

        LayoutModule::addHeadItem("this is some html");
        LayoutModule::addHeadItem("this is more html");

        $head = LayoutModule::getHeadItemsAsHtml();

        $this->assertEquals("this is some html
this is more html", $head);

    }

    public function testBodyItems()
    {
        LayoutModule::addBodyItem("this is some html");
        LayoutModule::addBodyItem("this is more html");

        $head = LayoutModule::getBodyItemsAsHtml();

        $this->assertEquals("this is some html
this is more html", $head);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        LayoutModule::setLayoutClassName("Rhubarb\Crown\Tests\Layout\TestLayout");
        LayoutModule::disableLayout();
    }
}
