<?php

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class WebRequestSSLOnTest extends RequestTestCase
{
    protected $request = null;

    protected function setUp()
    {
        parent::setUp();

        // inject some data for testing and cover the absence of web server-type
        // superglobals in the testing CLI context
        $_SERVER['HTTP_HOST'] = 'gcdtech.com';
        $_SERVER['SCRIPT_URI'] = 'http://gcdtech.com/foo';
        $_SERVER['SCRIPT_URL'] = '/foo';
        $_SERVER['HTTPS'] = 'https';

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

    public function testSSL()
    {
        $this->assertTrue($this->request->isSSL());
    }
}