<?php

namespace Gcd\Core\UrlHandlers;

use Gcd\Core\Module;
use Gcd\Core\Request\Request;
use Gcd\Core\Request\WebRequest;
use Gcd\Core\Response\HtmlResponse;
use Gcd\Core\UnitTesting\CoreTestCase;

class UrlHandlerTest extends CoreTestCase
{
	public function testUrlPriorities()
	{
		// Our test case has setup a handler which should come before the validate login handlers.
		$request = new WebRequest();
		$request->UrlPath = "/priority-test/simple/";

		$response = Module::GenerateResponseForRequest( $request );

		$this->assertNotInstanceOf( "Gcd\Core\Response\RedirectResponse", $response );
	}

	public function testChildHandler()
	{
		$child = new ClassMappedUrlHandler( "Gcd\Core\UnitTesting\NamespaceMappedHandlerTests\SubFolder\ObjectB" );
		$parent = new ClassMappedUrlHandler( "Gcd\Core\UnitTesting\NamespaceMappedHandlerTests\ObjectA", [ "child/" => $child ] );
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
	 * @param \Gcd\Core\Request\Request $request
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
	 * @param \Gcd\Core\Request\Request $request
	 * @param string $currentUrlFragment
	 * @return bool
	 */
	protected function GetMatchingUrlFragment( Request $request, $currentUrlFragment = "" )
	{
		return ( stripos( $currentUrlFragment, $this->stub ) === 0 );
	}
}
