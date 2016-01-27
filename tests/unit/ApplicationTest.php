<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModule;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModuleB;
use Rhubarb\Crown\Tests\Fixtures\SimpleContent;

class ApplicationTest extends \Codeception\TestCase\Test
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
}