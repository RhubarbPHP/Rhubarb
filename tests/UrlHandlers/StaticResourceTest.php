<?php

namespace Gcd\Tests;

use Gcd\Core\StaticResource\UrlHandlers\StaticResourceUrlHandler;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class StaticResourceTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
	protected $_request = null;

	public function setUp()
	{
		$this->_request = \Gcd\Core\Context::CurrentRequest();
		$this->_request->IsWebRequest = true;
	}

	public function tearDown()
	{
		$this->_request = null;
	}

	public function testStaticFileReturned()
	{
		$handler = new StaticResourceUrlHandler( __DIR__."/../UnitTesting/test.txt" );
		$handler->SetUrl( "/test.txt" );

		$this->_request->UrlPath = "/";

		$response = $handler->GenerateResponse( $this->_request );

		$this->assertFalse( $response );

		$this->_request->UrlPath = "/test.txt";
		$response = $handler->GenerateResponse( $this->_request );

		$this->assertEquals( "This is a static resource", $response->GetContent() );
	}

	public function testStaticFileDisablesLayout()
	{
		\Gcd\Core\Layout\LayoutModule::EnableLayout();

		$handler = new StaticResourceUrlHandler( __DIR__."/../UnitTesting/test.txt" );
		$handler->SetUrl( "/test.txt" );
		$this->_request->UrlPath = "/test.txt";

		$handler->GenerateResponse( $this->_request );

		$this->assertTrue( \Gcd\Core\Layout\LayoutModule::IsDisabled() );
	}

	public function testStaticFolderFindsAndReturnsFiles()
	{
		$handler = new StaticResourceUrlHandler( __DIR__."/../UnitTesting/" );
		$handler->SetUrl( "/files/" );
		$this->_request->UrlPath = "/files/test2.txt";

		$response = $handler->GenerateResponse( $this->_request );
		$this->assertEquals( "This is another static resource", $response->GetContent() );

		$this->_request->UrlPath = "/files/subfolder/test3.txt";
		$response = $handler->GenerateResponse( $this->_request );
		$this->assertEquals( "test3", $response->GetContent() );
	}

	public function testExceptionThrownIfPathDoesntExist()
	{
		$this->setExpectedException( "\Gcd\Core\StaticResource\Exceptions\StaticResourceNotFoundException" );

		new StaticResourceUrlHandler( __DIR__."/../UnitTesting/non-extant-file.txt" );
	}

	public function testExceptionThrownIfFileNotFoundInDirectory()
	{
		$this->setExpectedException( "\Gcd\Core\StaticResource\Exceptions\StaticResource404Exception" );

		$handler = new StaticResourceUrlHandler( __DIR__."/../UnitTesting/" );
		$handler->SetUrl( "/files/" );

		$this->_request->UrlPath = "/files/non-extant.txt";

		$handler->GenerateResponse( $this->_request );
	}

	public function testMimeTypeSetCorrectly()
	{
		$handler = new StaticResourceUrlHandler( __DIR__."/../UnitTesting/" );
		$handler->SetUrl( "/files/" );

		$this->_request->UrlPath = "/files/test.txt";
		$response = $handler->GenerateResponse( $this->_request );
		$headers = $response->GetHeaders();

		$this->assertArrayHasKey( "Content-Type", $headers );
		$this->assertEquals( "text/plain; charset=us-ascii", $headers[ "Content-Type" ] );

		$this->_request->UrlPath = "/files/base.css";
		$response = $handler->GenerateResponse( $this->_request );
		$headers = $response->GetHeaders();

		$this->assertArrayHasKey( "Content-Type", $headers );
		$this->assertEquals( "text/css", $headers[ "Content-Type" ] );

		$handler = new StaticResourceUrlHandler( __DIR__."/../../ClientSide/Resources/resource-manager.js" );
		$handler->SetUrl( "/js/resource-manager.js" );

		$this->_request->UrlPath = "/js/resource-manager.js";

		$response = $handler->GenerateResponse( $this->_request );
		$headers = $response->GetHeaders();

		$this->assertEquals( "application/javascript; charset=us-ascii", $headers[ "Content-Type" ] );
	}
}
