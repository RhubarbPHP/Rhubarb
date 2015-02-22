<?php

namespace Rhubarb\Crown\Tests\Sessions;

use Rhubarb\Crown\Sessions\Session;
use Rhubarb\Crown\Tests\RhubarbTestCase;

/**
 *
 * Note for unit tests for loading and saving of sessions look to the
 * test cases for the individual session provider type.
 */
class SessionTest extends RhubarbTestCase
{
	public function setUp()
	{
		Session::setDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider" );

		parent::setUp();
	}

	public function testDefaultSessionProvider()
	{
		$this->assertEquals( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider", Session::GetDefaultSessionProviderClassName() );

		Session::setDefaultSessionProviderClassName( "Rhubarb\Crown\Tests\Sessions\UnitTestingSessionProvider" );

		$this->assertEquals( "Rhubarb\Crown\Tests\Sessions\UnitTestingSessionProvider", Session::GetDefaultSessionProviderClassName() );

		$this->setExpectedException( "Rhubarb\Crown\Sessions\Exceptions\SessionProviderNotFoundException" );

		Session::setDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\UnknownProvider" );
	}

	public function testSessionGetsProvider()
	{
		Session::setDefaultSessionProviderClassName( "Rhubarb\Crown\Tests\Sessions\UnitTestingSessionProvider" );

		$session = new UnitTestingSession();

		$this->assertInstanceOf( "Rhubarb\Crown\Tests\Sessions\UnitTestingSessionProvider", $session->testGetSessionProvider() );

		Session::setDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider" );

		// Although we have changed the default provider, we already instantiated the session so the provider will not
		// have changed
		$this->assertInstanceOf( "Rhubarb\Crown\Tests\Sessions\UnitTestingSessionProvider", $session->testGetSessionProvider() );
	}


}
