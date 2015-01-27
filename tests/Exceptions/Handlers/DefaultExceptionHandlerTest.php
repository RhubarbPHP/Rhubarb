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

namespace Rhubarb\Crown\Exceptions\Handlers;

use Rhubarb\Crown\Exceptions\RhubarbException;
use Rhubarb\Crown\Layout\Layout;
use Rhubarb\Crown\Layout\LayoutModule;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;
use Rhubarb\Crown\UnitTesting\UnitTestLog;
use Rhubarb\Crown\UrlHandlers\UrlHandler;

class DefaultExceptionHandlerTest extends RhubarbTestCase
{
	/**
	 * @var UnitTestLog
	 */
	private static $_log;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		Module::RegisterModule( new UnitTestExceptionModule() );
		Module::InitialiseModules();

		self::$_rolesModule->ClearUrlHandlers();

		Log::ClearLogs();
		Log::AttachLog( self::$_log = new UnitTestLog( Log::ERROR_LEVEL ) );
	}

	public function testExceptionCausesLogEntry()
	{
		$request = new WebRequest();
		$request->UrlPath = "/test-exception/";

		Module::GenerateResponseForRequest( $request );

		$lastEntry = array_pop( self::$_log->entries );

		$this->assertEquals( "Unhandled CoreException `Things went wrong`", $lastEntry[0], "A CoreException should have been logged" );

		ExceptionHandler::SetExceptionHandlerClassName( '\Rhubarb\Crown\Exceptions\Handlers\UnitTestSilentExceptionHandler' );

		// Clear the log entries.
		self::$_log->entries = [];

		Module::GenerateResponseForRequest( $request );

		$this->assertCount( 0, self::$_log->entries, "The silent exception handler shouldn't log anything - exception handler injection is broken" );

		ExceptionHandler::SetExceptionHandlerClassName( '\Rhubarb\Crown\Exceptions\Handlers\DefaultExceptionHandler' );
	}

	public function testNonCoreExceptionCausesLogEntry()
	{
		$request = new WebRequest();
		$request->UrlPath = "/test-exception-non-core/";

		Module::GenerateResponseForRequest( $request );

		$lastEntry = array_pop( self::$_log->entries );

		$this->assertEquals( "Unhandled NonCoreException `OutOfBoundsException - Out of bounds`", $lastEntry[0], "A NonCoreException should have been logged" );
	}

	public function testPhpRuntimeErrorCausesLogEntry()
	{
		$request = new WebRequest();
		$request->UrlPath = "/test-php-error/";

		Module::GenerateResponseForRequest( $request );

		$lastEntry = array_pop( self::$_log->entries );

		$this->assertEquals( "Unhandled NonCoreException `PHPUnit_Framework_Error_Warning - Division by zero`", $lastEntry[0], "A NonCoreException should have been logged for php run time errors" );
	}

	public function testUrlHandlerGeneratesResponse()
	{
		// Enable layouts for this test as proof the URL handler has intercepted the response.
		LayoutModule::EnableLayout();

		$request = new WebRequest();
		$request->UrlPath = "/test-exception/";

		$response = Module::GenerateResponseForRequest( $request );

		$this->assertEquals( "TopSorry, something went wrong and we couldn't complete your request. The developers have
been notified.Tail", $response->GetContent() );

		LayoutModule::DisableLayout();
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
		$this->AddUrlHandlers(
			[
				"/test-exception/" => $h1 = new UnitTestCrashingHandler(),
				"/test-exception-non-core/" => $h2 = new UnitTestCrashingHandlerNonCoreException(),
				"/test-php-error/" => $h3 = new UnitTestPhpErrorHandler()
			]
		);

		$h1->SetPriority( 100 );
		$h2->SetPriority( 100 );
		$h3->SetPriority( 100 );
	}
}

class UnitTestCrashingHandler extends UrlHandler
{
	protected function GenerateResponseForRequest($request = null)
	{
		throw new RhubarbException( "Things went wrong" );
	}
}

class UnitTestCrashingHandlerNonCoreException extends UrlHandler
{
	protected function GenerateResponseForRequest($request = null)
	{
		throw new \OutOfBoundsException( "Out of bounds" );
	}
}

class UnitTestPhpErrorHandler extends UrlHandler
{
	protected function GenerateResponseForRequest($request = null)
	{
		// This will throw a run time error that we should be able to catch and handle.
		$x = 9 / 0;
	}
}

class UnitTestSilentExceptionHandler extends DefaultExceptionHandler
{
	protected function HandleException(RhubarbException $er)
	{
		// Do nothing!
	}
}