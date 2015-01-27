<?php

namespace Gcd\Tests;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class PhpSessionProviderTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
	public function testSessionStorage()
	{
		$session = new \Gcd\Core\Sessions\UnitTesting\UnitTestingSession();
		$session->TestValue = "abc123";
		$session->StoreSession();

		$this->assertEquals( "abc123", $_SESSION[ "UnitTestingSession" ][ "TestValue" ] );
	}

	public function testSessionRestore()
	{
		$session = new \Gcd\Core\Sessions\UnitTesting\UnitTestingSession();
		$session->TestValue = "abc123";
		$session->StoreSession();

		// We can't test PHP sessions properly within the same script. However we can verify
		// that it at least restores the data from the $_SESSION array
		\Gcd\Core\Settings::DeleteSettingNamespace( "UnitTestingSession" );

		$session = new \Gcd\Core\Sessions\UnitTesting\UnitTestingSession();

		$this->assertEquals( "abc123", $session->TestValue );
	}
}
