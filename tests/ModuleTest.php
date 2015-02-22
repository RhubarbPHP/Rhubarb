<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Module;

class ModuleTest extends RhubarbTestCase
{
    public function testModulesCanBeCleared()
    {
        Module::clearModules();

        $this->assertCount(0, Module::getAllModules());

        Module::registerModule(new TestModule2());
        Module::clearModules();

        $this->assertCount(0, Module::getAllModules());
    }

    public function testModulesAddToCollection()
    {
        Module::clearModules();

        Module::registerModule(new TestModule2());
        Module::registerModule(new TestModule3());

        $modules = Module::getAllModules();

        $this->assertCount(2, $modules);
        $this->assertEquals("Rhubarb\Crown\Tests\TestModule2", get_class($modules["Rhubarb\Crown\Tests\TestModule2"]));
    }

    public function testAllResponseFiltersReturned()
    {
        Module::clearModules();

        Module::registerModule(new LayoutModule("libraries/core/modules/Layout/UnitTesting/test-layout.php"));
        Module::registerModule(new TestModule3());
        Module::initialiseModules();

        $allFilters = Module::getAllResponseFilters();

        // Note that the layout module registers a response filter.
        $this->assertCount(2, $allFilters);
        $this->assertInstanceOf("\Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter", $allFilters[0]);
        $this->assertInstanceOf("\Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter", $allFilters[1]);
    }

    public function testCanRegisterDependantModules()
    {
        Module::clearModules();

        Module::registerModule(new TestModule4());

        $modules = Module::getAllModules();

        $this->assertArrayHasKey("Rhubarb\Crown\Tests\TestModule5", $modules);
        $this->assertArrayHasKey("Rhubarb\Crown\Tests\TestModule4", $modules);

        $values = array_values($modules);
        // Make sure the order is right too!
        $this->assertInstanceOf("Rhubarb\Crown\Tests\TestModule5", $values[0]);
        $this->assertInstanceOf("Rhubarb\Crown\Tests\TestModule4", $values[1]);
    }
}

class TestModule extends Module
{

}

class TestModule2 extends Module
{
    protected function Initialise()
    {
        parent::Initialise();

        $this->responseFilters[] = new LayoutFilter();
    }
}

class TestModule3 extends Module
{
    protected function Initialise()
    {
        parent::Initialise();

        $this->responseFilters[] = new LayoutFilter();
    }
}

class TestModule4 extends Module
{
    protected function RegisterDependantModules()
    {
        Module::RegisterModule(new TestModule5());
    }
}

class TestModule5 extends Module
{
}