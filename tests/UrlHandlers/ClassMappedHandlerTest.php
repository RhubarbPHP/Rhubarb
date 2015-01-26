<?php

namespace Gcd\Core\UrlHandlers;

use Gcd\Core\IGeneratesResponse;
use Gcd\Core\Request\WebRequest;
use Gcd\Core\Response\Response;
use Gcd\Core\UnitTesting\CoreTestCase;

class ClassMappedHandlerTest extends CoreTestCase
{
	public function testUrlHandled()
	{
		$request = new WebRequest();
		$request->UrlPath = "/wrong/path/";

		$handler = new ClassMappedUrlHandler( "\Gcd\Core\UrlHandlers\TestTarget" );
		$handler->SetUrl( "/right/path/" );

		$response = $handler->GenerateResponse( $request );

		$this->assertFalse( $response );

		$request = new WebRequest();
		$request->UrlPath = "/right/path/";

		$response = $handler->GenerateResponse( $request );

		$this->assertEquals( "bing bang bong", $response->GetContent() );
	}
}

class TestTarget implements IGeneratesResponse
{
	public function GenerateResponse( $request = null )
	{
		$response = new Response();
		$response->SetContent( "bing bang bong" );

		return $response;
	}
}