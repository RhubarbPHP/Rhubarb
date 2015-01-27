<?php

namespace Rhubarb\Crown\UrlHandlers;

use Rhubarb\Crown\Module;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\UnitTesting\RhubarbTestCase;

class UrlHandlerTest extends RhubarbTestCase
{
	public function testUrlPriorities()
	{
		// Our test case has setup a handler which should come before the validate login handlers.
		$request = new WebRequest();
		$request->UrlPath = "/priority-test/simple/";

		$response = Module::GenerateResponseForRequest( $request );

		$this->assertNotInstanceOf( "Rhubarb\Crown\Response\RedirectResponse", $response );
	}

	public function testChildHandler()
	{
		$child = new ClassMappedUrlHandler( "Rhubarb\Crown\UnitTesting\NamespaceMappedHandlerTests\SubFolder\ObjectB" );
		$parent = new ClassMappedUrlHandler( "Rhubarb\Crown\UnitTesting\NamespaceMappedHandlerTests\ObjectA", [ "child/" => $child ] );
		$parent->SetUrl( "/parent/" );

		$request = new WebRequest();
		$request->UrlPath = "/parent/child/";

		$response = $parent->GenerateResponse( $request );

		$this->assertEquals( "ObjectB Response", $response );

		$request->UrlPath = "/parent/not-child/";

		$response = $parent->GenerateResponse( $request );

		$this->assertEquals( "ObjectA Response", $response );

		$request->UrlPath = "/not-parent/not-child/";

		$response = $parent->GenerateResponse( $request );

		$this->assertFalse( $response );
	}
}

class TestParentHandler extends UrlHandler
{
	public $stub = "/";

	/**
	 * Return the response when appropriate or false if no response could be generated.
	 *
	 * @param mixed $request
	 * @param string $currentUrlFragment
	 * @return bool
	 */
	protected function GenerateResponseForRequest($request = null, $currentUrlFragment = "" )
	{
		$response = new HtmlResponse();
		$response->SetContent( "parent" );

		return $response;
	}

	/**
	 * Should be implemented to return a true or false as to whether this handler supports the given request.
	 *
	 * Normally this involves testing the request URI.
	 *
	 * @param \Rhubarb\Crown\Request\Request $request
	 * @param string $currentUrlFragment
	 * @return bool
	 */
	protected function GetMatchingUrlFragment(Request $request, $currentUrlFragment = "" )
	{
		return ( stripos( $currentUrlFragment, $this->stub ) === 0 );
	}
}

class TestChildHandler extends UrlHandler
{
	public $stub = "/";

	/**
	 * Return the response when appropriate or false if no response could be generated.
	 *
	 * @param mixed $request
	 * @param string $currentUrlFragment
	 * @return bool
	 */
	protected function GenerateResponseForRequest( $request = null, $currentUrlFragment = "" )
	{
		$response = new HtmlResponse();
		$response->SetContent( "child" );

		return $response;
	}

	/**
	 * Should be implemented to return a true or false as to whether this handler supports the given request.
	 *
	 * Normally this involves testing the request URI.
	 *
	 * @param \Rhubarb\Crown\Request\Request $request
	 * @param string $currentUrlFragment
	 * @return bool
	 */
	protected function GetMatchingUrlFragment( Request $request, $currentUrlFragment = "" )
	{
		return ( stripos( $currentUrlFragment, $this->stub ) === 0 );
	}
}
