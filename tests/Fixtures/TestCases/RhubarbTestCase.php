<?php

namespace Rhubarb\Crown\Tests\Fixtures\TestCases;

use Rhubarb\Crown\Application;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
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

    /**
     * @var Application
     */
    protected $application;

    protected function setUp()
    {
        $this->application = new Application();
        $this->application->unitTesting = true;
        $this->application->getPhpContext()->simulateNonCli = false;
        $this->application->registerModule(new UnitTestingModule());
        $this->application->initialiseModules();
        ExceptionHandler::disableExceptionTrapping();
    }

    protected function tearDown()
    {
    }
}