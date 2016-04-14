<?php

namespace Rhubarb\Crown\Tests;

use Codeception\TestCase\Test;
use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\Handlers\DefaultExceptionHandler;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionSettings;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModule;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModuleB;
use Rhubarb\Crown\Tests\Fixtures\SimpleContent;

class ApplicationTest extends Test
{
    public function testApplicationCanHaveModules()
    {
        $application = new Application();
        $application->registerModule(new UnitTestingModule());

        $this->assertCount(3, $application->getRegisteredModules());
        $this->assertInstanceOf(LayoutModule::class, $application->getRegisteredModules()[1]);
        $this->assertInstanceOf(UnitTestingModule::class, $application->getRegisteredModules()[2]);

        $secondModule = new UnitTestingModuleB();
        $secondModule->foo = "bar";

        $application->registerModule($secondModule);

        $this->assertCount(4, $application->getRegisteredModules());

        $secondModule = new UnitTestingModuleB();
        $secondModule->foo = "bing";

        $application->registerModule($secondModule);

        $this->assertCount(4, $application->getRegisteredModules());
        $this->assertEquals("bing", $application->getRegisteredModules()[3]->foo);
    }

    public function testApplicationRuns()
    {
        $application = new Application();
        $application->registerModule(new UnitTestingModule());

        $request = new WebRequest();
        $request->urlPath = "/";

        $response = $application->generateResponseForRequest($request);

        $this->assertContains(SimpleContent::CONTENT, $response->getContent());
    }

    public function testRunningApplication()
    {
        $application = new Application();

        $this->assertEquals($application, Application::current());

        $application2 = new Application();

        $this->assertEquals($application2, Application::current());

        $request = new WebRequest();
        $request->urlPath = "/";

        $application->generateResponseForRequest($request);

        $this->assertEquals($application, Application::current());
    }

    public function testApplicationPath()
    {
        $application = new Application();
        $this->assertEquals(realpath(VENDOR_DIR."/../"), $application->applicationRootPath);
    }

    public function testApplicationData()
    {
        $application = new Application();
        $array = &$application->getSharedArray("test-key");
        $array["key1"] = "value1";

        $array2 = &$application->getSharedArray("test-key");

        $this->assertEquals("value1", $array2["key1"]);

        $array2["that"] = "this";

        $this->assertEquals("this", $array["that"]);
    }
}