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
        $this->application->context()->simulateNonCli = false;
        $this->application->registerModule(new UnitTestingModule());
        $this->application->initialiseModules();


        ExceptionHandler::disableExceptionTrapping();
    }

    protected function tearDown()
    {
    }
}