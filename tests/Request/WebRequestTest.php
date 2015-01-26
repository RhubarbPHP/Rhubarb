<?php

namespace Gcd\Tests;

/**
 * @author marramgrass
 * @copyright GCD Technologies 2012
 */
class WebRequestTest extends \Gcd\Core\Request\UnitTesting\RequestTestCase
{
	protected $_request = null;

	protected function setUp()
	{
		parent::setUp();

		// inject some data for testing and cover the absence of web server-type
		// superglobals in the testing CLI context
		$_SERVER[ 'HTTP_HOST' ] = 'gcdtech.com';
		$_SERVER[ 'SCRIPT_URI' ] = 'http://gcdtech.com/foo';
		$_SERVER[ 'REQUEST_URI' ] = '/foo';
		$_SERVER[ 'SCRIPT_NAME' ] = '/foo';

		$_GET = [];
		$_POST = [];
		$_FILES = [];
		$_COOKIE = [];
		$_SESSION = [];
		$_REQUEST = [];

		$this->_request = new \Gcd\Core\Request\WebRequest();
	}

	protected function tearDown()
	{
		$this->_request = null;

		parent::tearDown();
	}

	public function testIsWebRequest()
	{
		$this->assertTrue( $this->_request->IsWebRequest );
	}

	public function testHostValue()
	{
		$this->assertEquals( 'gcdtech.com', $this->_request->Host );
	}

	public function testURIValue()
	{
		$this->assertEquals( 'http://gcdtech.com/foo', $this->_request->URI );
	}

	public function testPathValue()
	{
		$this->assertEquals( '/foo', $this->_request->UrlPath );
	}

	public function testNoSSL()
	{
		$this->assertFalse( $this->_request->IsSSL );
	}
}
