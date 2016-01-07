<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\Exceptions\Handlers;

use Rhubarb\Crown\Exceptions\Handlers\DefaultExceptionHandler;
use Rhubarb\Crown\Exceptions\Handlers\ExceptionHandler;
use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\unit\Logging\UnitTestLog;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class DefaultExceptionHandlerTest extends RhubarbTestCase
{
    /**
     * @var UnitTestLog
     */
    private static $log;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        Module::registerModule(new UnitTestExceptionModule());
        Module::initialiseModules();

        Log::clearLogs();
        Log::attachLog(self::$log = new UnitTestLog(Log::ERROR_LEVEL));

        ExceptionHandler::enableExceptionTrapping();
    }

    protected function setUp()
    {
        parent::setUp();

        ExceptionHandler::setExceptionHandlerClassName(DefaultExceptionHandler::class);
    }

    public function testExceptionCausesLogEntry()
    {
        $request = new WebRequest();
        $request->UrlPath = "/test-exception/";

        Module::generateResponseForRequest($request);

        $lastEntry = array_pop(self::$log->entries);

        $this->assertContains('Unhandled Rhubarb\Crown\Exceptions\RhubarbException `Things went wrong`', $lastEntry[0],
            "A RhubarbException should have been logged");

        ExceptionHandler::setExceptionHandlerClassName(UnitTestSilentExceptionHandler::class);

        // Clear the log entries.
        self::$log->entries = [];

        Module::generateResponseForRequest($request);

        $this->assertCount(0, self::$log->entries,
            "The silent exception handler shouldn't log anything - exception handler injection is broken");
    }

    public function testNonRhubarbExceptionCausesLogEntry()
    {
        $request = new WebRequest();
        $request->UrlPath = "/test-exception-non-core/";

        Module::generateResponseForRequest($request);

        $lastEntry = array_pop(self::$log->entries);

        $this->assertContains('Unhandled Rhubarb\Crown\Exceptions\NonRhubarbException `OutOfBoundsException - Out of bounds`',
            $lastEntry[0],
            "A NonRhubarbException should have been logged");
    }

    public function testPhpRuntimeErrorCausesLogEntry()
    {
        $request = new WebRequest();
        $request->UrlPath = "/test-php-error/";

        Module::generateResponseForRequest($request);

        $lastEntry = array_pop(self::$log->entries);

        $this->assertContains('Unhandled Rhubarb\Crown\Exceptions\NonRhubarbException `ErrorException - Division by zero`',
            $lastEntry[0], "A NonRhubarbException should have been logged for php run time errors");
    }

    public function testUrlHandlerGeneratesResponse()
    {
        // Enable layouts for this test as proof the URL handler has intercepted the response.
        LayoutModule::enableLayout();

        $request = new WebRequest();
        $request->UrlPath = "/test-exception/";

        $response = Module::generateResponseForRequest($request);

        $this->assertEquals("TopSorry, something went wrong and we couldn't complete your request. The developers have
been notified.Tail", $response->getContent());

        LayoutModule::disableLayout();
    }

    public function testDisablingTrapping()
    {
        ExceptionHandler::disableExceptionTrapping();

        try {
            // Enable layouts for this test as proof the URL handler has intercepted the response.
            LayoutModule::enableLayout();

            $request = new WebRequest();
            $request->UrlPath = "/test-exception/";

            $response = Module::generateResponseForRequest($request);
            $this->fail("Without exception trapping this line should not be reached.");
        } catch (RhubarbException $er) {
        }

        ExceptionHandler::setExceptionHandlerClassName(UnitTestDisobedientExceptionHandler::class);

        try {
            // Enable layouts for this test as proof the URL handler has intercepted the response.
            LayoutModule::enableLayout();

            $request = new WebRequest();
            $request->UrlPath = "/test-exception/";

            $response = Module::generateResponseForRequest($request);

        } catch (RhubarbException $er) {
            $this->fail("The extended exception handler should force handling of exceptions even if trapping is disabled.");
        }

        ExceptionHandler::enableExceptionTrapping();

        LayoutModule::disableLayout();
    }
}

class UnitTestExceptionModule extends Module
{
    public function __construct()
    {
        parent::__construct();

        $this->namespace = __NAMESPACE__;
    }

    protected function RegisterUrlHandlers()
    {
        $this->addUrlHandlers(
            [
                "/test-exception/" => $h1 = new UnitTestCrashingHandler(),
                "/test-exception-non-core/" => $h2 = new UnitTestCrashingHandlerNonRhubarbException(),
                "/test-php-error/" => $h3 = new UnitTestPhpErrorHandler()
            ]
        );

        $h1->setPriority(100);
        $h2->setPriority(100);
        $h3->setPriority(100);
    }
}

class UnitTestCrashingHandler extends UrlHandler
{
    protected function generateResponseForRequest($request = null)
    {
        throw new RhubarbException("Things went wrong");
    }
}

class UnitTestCrashingHandlerNonRhubarbException extends UrlHandler
{
    protected function generateResponseForRequest($request = null)
    {
        throw new \OutOfBoundsException("Out of bounds");
    }
}

class UnitTestPhpErrorHandler extends UrlHandler
{
    protected function generateResponseForRequest($request = null)
    {
        // This will throw a run time error that we should be able to catch and handle.
        $x = 9 / 0;
    }
}

class UnitTestSilentExceptionHandler extends DefaultExceptionHandler
{
    protected function handleException(RhubarbException $er)
    {
        // Do nothing!
    }
}

class UnitTestDisobedientExceptionHandler extends DefaultExceptionHandler
{
    protected function shouldTrapException(RhubarbException $er)
    {
        // Force handling of exceptions - even if disabled! Naughty!
        return true;
    }
}