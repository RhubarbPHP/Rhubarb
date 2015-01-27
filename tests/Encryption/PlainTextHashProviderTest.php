<?php

namespace Gcd\Core\Encryption;

use Gcd\Core\UnitTesting\CoreTestCase;

class PlainTextHashProviderTest extends CoreTestCase
{
	public function testProvider()
	{
		$plainTextProvider = new PlainTextHashProvider();
		$result = $plainTextProvider->CreateHash( "abc123", "" );

		$this->assertEquals( "abc123", $result );

		$this->assertTrue( $plainTextProvider->CompareHash( "abc123", "abc123" ) );
	}

}
