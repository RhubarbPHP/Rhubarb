<?php

namespace Gcd\Tests;

use Rhubarb\Crown\HttpHeaders;
use Rhubarb\Crown\Module;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class NamespaceMappedHandlerTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
	protected $_request = null;

	protected function setUp()
	{
		$this->_request = \Rhubarb\Crown\Context::CurrentRequest();
		$this->_request->IsWebRequest = true;
	}

	public function testHandlerFindsTestObject()
	{
		$this->_request->UrlPath = "/nmh/ObjectA/";

		$response = Module::GenerateResponseForRequest( $this->_request );
		$this->assertEquals( "ObjectA Response", $response->GetContent() );

		$this->_request->UrlPath = "/nmh/SubFolder/ObjectB/";

		$response = Module::GenerateResponseForRequest( $this->_request );
		$this->assertEquals( "ObjectB Response", $response->GetContent() );
	}

	public function testHandlerRedirectsWhenTrailingSlashMissing()
	{
		$this->_request->UrlPath = "/nmh/ObjectA";

		$response = Module::GenerateResponseForRequest( $this->_request );

		$headers = $response->GetHeaders();

		$this->assertEquals( "/nmh/ObjectA/", $headers[ "Location" ] );
	}

	public function testHandlerRedirectsToIndexPage()
	{
		HttpHeaders::ClearHeaders();

		// This folder does contain an index so it should redirect.
		$this->_request->UrlPath = "/nmh/SubFolder/";

		$response = Module::GenerateResponseForRequest( $this->_request );

		$headers = $response->GetHeaders();

		$this->assertEquals( "/nmh/SubFolder/index/", $headers[ "Location" ] );
	}

	public function testHandlerFindsAbsoluteClassName()
	{
		$this->_request->UrlPath = "/Gcd/Core/UnitTesting/NamespaceMappedHandlerTests/ObjectA";

		$response = Module::GenerateResponseForRequest( $this->_request );

		$this->assertEquals( "ObjectA Response", $response->GetContent() );
	}
}
