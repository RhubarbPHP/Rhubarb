<?php

namespace Gcd\Tests;

/**
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class HttpHeadersTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
	public function testHeadersAreSet()
	{
		\Gcd\Core\HttpHeaders::ClearHeaders();
		\Gcd\Core\HttpHeaders::SetHeader( "Content-type", "text/plain" );

		$headers = \Gcd\Core\HttpHeaders::GetHeaders();

		$this->assertCount( 1, $headers );
		$this->assertEquals( "text/plain", $headers[ "Content-type" ] );

		\Gcd\Core\HttpHeaders::SetHeader( "Content-length", "2048" );

		$headers = \Gcd\Core\HttpHeaders::GetHeaders();

		$this->assertCount( 2, $headers );
		$this->assertEquals( "2048", $headers[ "Content-length" ] );

		\Gcd\Core\HttpHeaders::SetHeader( "Content-type", "text/xml" );

		$headers = \Gcd\Core\HttpHeaders::GetHeaders();

		$this->assertCount( 2, $headers );
		$this->assertEquals( "text/xml", $headers[ "Content-type" ] );
	}

	public function testHeadersAreFlushed()
	{
		\Gcd\Core\HttpHeaders::ClearHeaders();
		\Gcd\Core\HttpHeaders::SetHeader( "Content-type", "text/plain" );
		\Gcd\Core\HttpHeaders::FlushHeaders();

		$headers = \Gcd\Core\HttpHeaders::GetHeaders();

		$this->assertTrue( \Gcd\Core\HttpHeaders::$flushed );
	}
}
