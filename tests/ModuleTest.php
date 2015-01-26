<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\CoreModule;
use Rhubarb\Crown\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testModulesCanBeCleared()
    {
        Module::ClearModules();

        $this->assertCount(0, Module::GetAllModules());

        Module::RegisterModule(new TestModule2());
        Module::ClearModules();

        $this->assertCount(0, Module::GetAllModules());
    }

    /**
     * @expectedException \Rhubarb\Crown\Exceptions\ImplementationException
     */
    public function testModuleWithoutNamespaceCrashes()
    {
        Module::RegisterModule(new TestModule());
    }

    public function testModulesAddToCollection()
    {
        Module::ClearModules();

        Module::RegisterModule(new TestModule2());
        Module::RegisterModule(new TestModule3());

        $modules = Module::GetAllModules();

        $this->assertCount(2, $modules);
        $this->assertEquals("Gcd\Tests\TestModule2", get_class($modules["Gcd\Tests\TestModule2"]));
    }

    public function testAddClassPathRequiresNamespace()
    {
        Module::ClearModules();

        $this->setExpectedException("\Rhubarb\Crown\Exceptions\ImplementationException");

        Module::RegisterModule(new BadModule());
    }

    public function testAddClassPathWarnsAboutSlashType()
    {
        Module::ClearModules();

        $this->setExpectedException("\Rhubarb\Crown\Exceptions\ImplementationException");

        Module::RegisterModule(new BadModule2());
    }

    public function testClassPathCanWorkWithAnyNamespace()
    {
        Module::ClearModules();

        Module::RegisterModule(new TestModule3());

        // This should not throw a wobbler.
        $object = new \Rhubarb\Crown\Find\Me\FindMe();
    }

    public function testAllResponseFiltersReturned()
    {
        Module::ClearModules();

        Module::RegisterModule(new \Rhubarb\Crown\CoreModule());

        Module::RegisterModule(new \Rhubarb\Crown\Layout\LayoutModule("libraries/core/modules/Layout/UnitTesting/test-layout.php"));
        Module::RegisterModule(new TestModule3());
        Module::InitialiseModules();

        $allFilters = Module::GetAllResponseFilters();

        // Note that the layout module registers a response filter.
        $this->assertCount(2, $allFilters);
        $this->assertInstanceOf("\Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter", $allFilters[0]);
        $this->assertInstanceOf("\Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter", $allFilters[1]);
    }

    public function testCanRegisterDependantModules()
    {
        Module::ClearModules();

        Module::RegisterModule(new CoreModule());
        Module::RegisterModule(new TestModule4());

        $modules = Module::GetAllModules();

        $this->assertArrayHasKey("Gcd\Tests\TestModule5", $modules);
        $this->assertArrayHasKey("Gcd\Tests\TestModule4", $modules);

        $values = array_values($modules);
        // Make sure the order is right too!
        $this->assertInstanceOf("Gcd\Tests\TestModule5", $values[1]);
        $this->assertInstanceOf("Gcd\Tests\TestModule4", $values[2]);
    }
}

class TestModule extends \Rhubarb\Crown\Module
{

}

class TestModule2 extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;
    }

    protected function Initialise()
    {
        parent::Initialise();

        $this->_responseFilters[] = new \Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter();
    }
}

class TestModule3 extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;

        $this->AddClassPath(__DIR__ . "/UnitTesting/DifferentNamespaceTest", "Rhubarb\Crown\Find\Me");
    }

    protected function Initialise()
    {
        parent::Initialise();

        $this->_responseFilters[] = new \Rhubarb\Crown\Layout\ResponseFilters\LayoutFilter();
    }
}

class TestModule4 extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;
    }

    protected function RegisterDependantModules()
    {
        Module::RegisterModule(new TestModule5());
    }
}

class TestModule5 extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;
    }
}

class BadModule extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        // This should throw an exception as it is called before a namespace is set.
        $this->AddClassPath("abc");

        $this->namespace = __NAMESPACE__;
    }
}

class BadModule2 extends \Rhubarb\Crown\Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;

        // This should throw an exception as it's namespace is using the wrong slash
        $this->AddClassPath("abc", "Gcd/Core/Path");
    }
}