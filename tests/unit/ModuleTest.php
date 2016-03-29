<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

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
        $this->assertEquals(TestModule2::class, get_class($modules[TestModule2::class]));
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
        $this->assertInstanceOf(LayoutFilter::class, $allFilters[0]);
        $this->assertInstanceOf(LayoutFilter::class, $allFilters[1]);
    }

    public function testCanRegisterDependantModules()
    {
        Module::clearModules();

        Module::registerModule(new TestModule4());

        $modules = Module::getAllModules();

        $this->assertArrayHasKey(TestModule5::class, $modules);
        $this->assertArrayHasKey(TestModule4::class, $modules);

        $values = array_values($modules);
        // Make sure the order is right too!
        $this->assertInstanceOf(TestModule5::class, $values[0]);
        $this->assertInstanceOf(TestModule4::class, $values[1]);
    }
}

class TestModule extends Module
{

}

class TestModule2 extends Module
{
    protected function initialise()
    {
        parent::initialise();

        $this->responseFilters[] = new LayoutFilter();
    }
}

class TestModule3 extends Module
{
    protected function initialise()
    {
        parent::initialise();

        $this->responseFilters[] = new LayoutFilter();
    }
}

class TestModule4 extends Module
{
    protected function registerDependantModules()
    {
        Module::registerModule(new TestModule5());
    }
}

class TestModule5 extends Module
{
}
