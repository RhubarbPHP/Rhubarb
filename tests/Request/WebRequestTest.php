<?php

namespace Rhubarb\Crown\Tests\Request;

use Rhubarb\Crown\Request\WebRequest;

/**
 * @author marramgrass
 * @copyright GCD Technologies 2012
 */
class WebRequestTest extends RequestTestCase
{
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        // inject some data for testing and cover the absence of web server-type
        // superglobals in the testing CLI context
        $_SERVER['HTTP_HOST'] = 'gcdtech.com';
        $_SERVER['SCRIPT_URI'] = 'http://gcdtech.com/foo';
        $_SERVER['REQUEST_URI'] = '/foo';
        $_SERVER['SCRIPT_NAME'] = '/foo';

        $_GET = [];
        $_POST = [];
        $_FILES = [];
        $_COOKIE = [];
        $_SESSION = [];
        $_REQUEST = [];

        $this->request = new WebRequest();
    }

    protected function tearDown()
    {
        $this->request = null;

        parent::tearDown();
    }

    public function testIsWebRequest()
    {
        $this->assertTrue($this->request->IsWebRequest);
    }

    public function testHostValue()
    {
        $this->assertEquals('gcdtech.com', $this->request->Host);
    }

    public function testURIValue()
    {
        $this->assertEquals('http://gcdtech.com/foo', $this->request->URI);
    }

    public function testPathValue()
    {
        $this->assertEquals('/foo', $this->request->UrlPath);
    }

    public function testNoSSL()
    {
        $this->assertFalse($this->request->IsSSL);
    }
}
