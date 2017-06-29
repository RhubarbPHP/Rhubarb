<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests;

use Codeception\Lib\Di;
use Codeception\Test\Unit;
use Rhubarb\Crown\Application;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModule;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModuleB;
use Rhubarb\Crown\Tests\Fixtures\SimpleContent;

class ApplicationTest extends Unit
{
    protected function setUp()
    {
        // This shim bridges support between codeception and phpstorm.
        $meta = $this->getMetadata();
        $meta->setServices(
            [
                "di" => new Di()
            ]
        );

        return parent::setUp();
    }

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
        $application->initialiseModules();

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