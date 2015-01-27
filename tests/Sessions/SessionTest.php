<?php

namespace Gcd\Tests;
use Rhubarb\Crown\Sessions\Session;

/**
 *
 * Note for unit tests for loading and saving of sessions look to the
 * test cases for the individual session provider type.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class SessionTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
	public function setUp()
	{
		Session::SetDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider" );

		parent::setUp();
	}

	public function testDefaultSessionProvider()
	{
		$this->assertEquals( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider", Session::GetDefaultSessionProviderClassName() );

		Session::SetDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSessionProvider" );

		$this->assertEquals( "Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSessionProvider", Session::GetDefaultSessionProviderClassName() );

		$this->setExpectedException( "Rhubarb\Crown\Sessions\Exceptions\SessionProviderNotFoundException" );

		Session::SetDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\UnknownProvider" );
	}

	public function testSessionGetsProvider()
	{
		Session::SetDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSessionProvider" );

		$session = new \Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSession();

		$this->assertInstanceOf( "Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSessionProvider", $session->TestGetSessionProvider() );

		Session::SetDefaultSessionProviderClassName( "Rhubarb\Crown\Sessions\SessionProviders\PhpSessionProvider" );

		// Although we have changed the default provider, we already instantiated the session so the provider will not
		// have changed
		$this->assertInstanceOf( "Rhubarb\Crown\Sessions\UnitTesting\UnitTestingSessionProvider", $session->TestGetSessionProvider() );
	}


}
