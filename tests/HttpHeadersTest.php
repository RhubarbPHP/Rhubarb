<?php

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\HttpHeaders;

class HttpHeadersTest extends RhubarbTestCase
{
	public function testHeadersAreSet()
	{
		HttpHeaders::ClearHeaders();
		HttpHeaders::SetHeader( "Content-type", "text/plain" );

		$headers = HttpHeaders::GetHeaders();

		$this->assertCount( 1, $headers );
		$this->assertEquals( "text/plain", $headers[ "Content-type" ] );

		HttpHeaders::SetHeader( "Content-length", "2048" );

		$headers = HttpHeaders::GetHeaders();

		$this->assertCount( 2, $headers );
		$this->assertEquals( "2048", $headers[ "Content-length" ] );

		HttpHeaders::SetHeader( "Content-type", "text/xml" );

		$headers = HttpHeaders::GetHeaders();

		$this->assertCount( 2, $headers );
		$this->assertEquals( "text/xml", $headers[ "Content-type" ] );
	}

	public function testHeadersAreFlushed()
	{
		HttpHeaders::ClearHeaders();
		HttpHeaders::SetHeader( "Content-type", "text/plain" );
		HttpHeaders::FlushHeaders();

		$headers = HttpHeaders::GetHeaders();

		$this->assertTrue( HttpHeaders::$flushed );
	}
}
