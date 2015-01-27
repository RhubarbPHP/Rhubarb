<?php

namespace Gcd\Tests;

/**
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class HtmlResponseTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
    protected $_response = null;

    protected function setUp()
    {
        $this->_response = new \Rhubarb\Crown\Response\HtmlResponse();
    }

    protected function tearDown()
    {
        $this->_response = null;
    }

    public function testDefaultContentTypeHeader()
    {
        $this->assertEquals($this->_response->GetHeaders()['Content-Type'], 'text/html',
            "Content-Type header is not set to text/html");
    }
}
