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

        $this->assertCount(2, $application->getModules());
        $this->assertInstanceOf(LayoutModule::class, $application->getModules()[0]);
        $this->assertInstanceOf(UnitTestingModule::class, $application->getModules()[1]);

        $secondModule = new UnitTestingModuleB();
        $secondModule->foo = "bar";

        $application->registerModule($secondModule);

        $this->assertCount(3, $application->getModules());

        $secondModule = new UnitTestingModuleB();
        $secondModule->foo = "bing";

        $application->registerModule($secondModule);

        $this->assertCount(3, $application->getModules());
        $this->assertEquals("bing", $application->getModules()[2]->foo);
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

    public function testDefaultExceptionHandlerEnabled()
    {
        $application = new Application();
        $instance = $application->container()->getInstance(ExceptionHandler::class);

        $this->assertInstanceOf(DefaultExceptionHandler::class, $instance);

        $instance->prop1 = true;

        $instance = $application->container()->getInstance(ExceptionHandler::class);

        $this->assertTrue($instance->prop1, "Should have been a singleton...");


        $instance = $application->container()->getInstance(ExceptionSettings::class);
        $instance->prop1 = true;

        $instance = $application->container()->getInstance(ExceptionSettings::class);

        $this->assertTrue($instance->prop1, "Should have been a singleton...");
    }
}