<?php

namespace Rhubarb\Crown\Tests\UrlHandlers;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\GeneratesResponse;
use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Crown\UrlHandlers\ClassMappedUrlHandler;

class ClassMappedHandlerTest extends RhubarbTestCase
{
	public function testUrlHandled()
	{
		$request = new WebRequest();
		$request->UrlPath = "/wrong/path/";

		$handler = new ClassMappedUrlHandler( "\Rhubarb\Crown\Tests\UrlHandlers\TestTarget" );
		$handler->SetUrl( "/right/path/" );

		$response = $handler->generateResponse( $request );

		$this->assertFalse( $response );

		$request = new WebRequest();
		$request->UrlPath = "/right/path/";

		$response = $handler->GenerateResponse( $request );

		$this->assertEquals( "bing bang bong", $response->getContent() );
	}
}

class TestTarget implements GeneratesResponse
{
	public function generateResponse( $request = null )
	{
		$response = new Response();
		$response->setContent( "bing bang bong" );

		return $response;
	}
}