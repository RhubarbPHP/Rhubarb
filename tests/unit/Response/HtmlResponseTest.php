<?php

namespace Rhubarb\Crown\Tests\unit\Response;

use Rhubarb\Crown\Response\HtmlResponse;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class HtmlResponseTest extends RhubarbTestCase
{
    protected $response = null;

    protected function setUp()
    {
        $this->response = new HtmlResponse();
    }

    protected function tearDown()
    {
        $this->response = null;
    }

    public function testDefaultContentTypeHeader()
    {
        $this->assertEquals($this->response->getHeaders()['Content-Type'], 'text/html',
            "Content-Type header is not set to text/html");
    }
}
