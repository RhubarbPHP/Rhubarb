<?php

namespace Rhubarb\Crown\UrlHandlers;

use Rhubarb\Crown\IGeneratesResponse;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class ClassMappedHandlerTest extends RhubarbTestCase
{
	public function testUrlHandled()
	{
		$request = new WebRequest();
		$request->UrlPath = "/wrong/path/";

		$handler = new ClassMappedUrlHandler( "\Rhubarb\Crown\UrlHandlers\TestTarget" );
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