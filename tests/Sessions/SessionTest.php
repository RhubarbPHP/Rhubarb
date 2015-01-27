<?php

namespace Gcd\Tests;
use Gcd\Core\Sessions\Session;

/**
 *
 * Note for unit tests for loading and saving of sessions look to the
 * test cases for the individual session provider type.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class SessionTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
	public function setUp()
	{
		Session::SetDefaultSessionProviderClassName( "Gcd\Core\Sessions\SessionProviders\PhpSessionProvider" );

		parent::setUp();
	}

	public function testDefaultSessionProvider()
	{
		$this->assertEquals( "Gcd\Core\Sessions\SessionProviders\PhpSessionProvider", Session::GetDefaultSessionProviderClassName() );

		Session::SetDefaultSessionProviderClassName( "Gcd\Core\Sessions\UnitTesting\UnitTestingSessionProvider" );

		$this->assertEquals( "Gcd\Core\Sessions\UnitTesting\UnitTestingSessionProvider", Session::GetDefaultSessionProviderClassName() );

		$this->setExpectedException( "Gcd\Core\Sessions\Exceptions\SessionProviderNotFoundException" );

		Session::SetDefaultSessionProviderClassName( "Gcd\Core\Sessions\SessionProviders\UnknownProvider" );
	}

	public function testSessionGetsProvider()
	{
		Session::SetDefaultSessionProviderClassName( "Gcd\Core\Sessions\UnitTesting\UnitTestingSessionProvider" );

		$session = new \Gcd\Core\Sessions\UnitTesting\UnitTestingSession();

		$this->assertInstanceOf( "Gcd\Core\Sessions\UnitTesting\UnitTestingSessionProvider", $session->TestGetSessionProvider() );

		Session::SetDefaultSessionProviderClassName( "Gcd\Core\Sessions\SessionProviders\PhpSessionProvider" );

		// Although we have changed the default provider, we already instantiated the session so the provider will not
		// have changed
		$this->assertInstanceOf( "Gcd\Core\Sessions\UnitTesting\UnitTestingSessionProvider", $session->TestGetSessionProvider() );
	}


}
