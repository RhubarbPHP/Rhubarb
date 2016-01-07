<?php

namespace Rhubarb\Crown\Tests\Fixtures\TestCases;
use Rhubarb\Crown\Context;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Tests\Fixtures\Modules\UnitTestingModule;

/**
 * This base class adds basic setup and teardown for unit testing within Rhubarb's core
 */
class RhubarbTestCase extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected static $rolesModule;

    protected function setUp()
    {
        Module::RegisterModule(new UnitTestingModule());
        Module::InitialiseModules();

        $context = new Context();
        $context->UnitTesting = true;
        $context->SimulateNonCli = false;

        $request = Context::CurrentRequest();
        $request->Reset();
    }

    protected function tearDown()
    {
        Module::clearModules();

        parent::tearDownAfterClass();
    }
}