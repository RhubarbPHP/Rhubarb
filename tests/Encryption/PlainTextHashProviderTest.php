<?php

namespace Rhubarb\Crown\Encryption;

use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class PlainTextHashProviderTest extends RhubarbTestCase
{
	public function testProvider()
	{
		$plainTextProvider = new PlainTextHashProvider();
		$result = $plainTextProvider->CreateHash( "abc123", "" );

		$this->assertEquals( "abc123", $result );

		$this->assertTrue( $plainTextProvider->CompareHash( "abc123", "abc123" ) );
	}

}
