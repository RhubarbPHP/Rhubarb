<?php

namespace Rhubarb\Crown\Tests\Response;

use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class HtmlResponseTest extends RhubarbTestCase
{
    protected $_response = null;

    protected function setUp()
    {
        $this->_response = new HtmlResponse();
    }

    protected function tearDown()
    {
        $this->_response = null;
    }

    public function testDefaultContentTypeHeader()
    {
        $this->assertEquals($this->_response->getHeaders()['Content-Type'], 'text/html',
            "Content-Type header is not set to text/html");
    }
}
