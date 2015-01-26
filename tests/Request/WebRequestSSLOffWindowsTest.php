<?php

namespace Gcd\Tests;

/**
 * @author    marramgrass
 * @copyright GCD Technologies 2012
 */
class WebRequestSSLOffWindowsTest extends \Gcd\Core\Request\UnitTesting\RequestTestCase
{

	protected $_request = null;

	protected function setUp()
	{
		parent::setUp();

		// inject some data for testing and cover the absence of web server-type
		// superglobals in the testing CLI context
		$_SERVER[ 'HTTP_HOST' ] = 'gcdtech.com';
		$_SERVER[ 'SCRIPT_URI' ] = 'http://gcdtech.com/foo';
		$_SERVER[ 'SCRIPT_URL' ] = '/foo';
		$_SERVER[ 'HTTPS' ] = 'off';

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

	public function testNoSSL()
	{
		$this->assertFalse( $this->_request->IsSSL );
	}
}
