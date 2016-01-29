<?php

namespace Rhubarb\Crown\Tests;

use Codeception\TestCase\Test;
use Rhubarb\Crown\Application;
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
        $request->UrlPath = "/";

        $response = $application->generateResponseForRequest($request);

        $this->assertContains(SimpleContent::CONTENT, $response->getContent());
    }

    public function testRunningApplication()
    {
        $application = new Application();
        $application->run();

        $this->assertEquals($application, Application::runningApplication());

        $application2 = new Application();
        $application2->run();

        $this->assertEquals($application2, Application::runningApplication());

        $request = new WebRequest();
        $request->UrlPath = "/";

        $application->generateResponseForRequest($request);

        $this->assertEquals($application, Application::runningApplication());
    }

    public function testApplicationPath()
    {
        $application = new Application();
        $this->assertEquals(realpath(VENDOR_DIR."/../"), $application->applicationRootPath);
    }
}