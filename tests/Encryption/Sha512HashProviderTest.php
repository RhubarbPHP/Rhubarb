<?php

namespace Gcd\Tests;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2013
 */
class Sha512HashProviderTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
	public function testHash()
	{
		$hasher = new \Rhubarb\Crown\Encryption\Sha512HashProvider();
		$result = $hasher->CreateHash( "abc123", "saltyfish" );

		$this->assertEquals( '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0', $result );
	}

	public function testHashesAreCompared()
	{
		$hasher = new \Rhubarb\Crown\Encryption\Sha512HashProvider();

		$hash = $hasher->CreateHash( "abc123", "saltyfish" );

		$result = $hasher->CompareHash( "abc123", $hash );
		$this->assertTrue( $result );

		$result = $hasher->CompareHash( "dep456", $hash );
		$this->assertFalse( $result );

		// Repeat the tests with an automated salt.
		$hash = $hasher->CreateHash( "abc123" );

		$result = $hasher->CompareHash( "abc123", $hash );
		$this->assertTrue( $result );

		$result = $hasher->CompareHash( "dep456", $hash );
		$this->assertFalse( $result );
	}
}
