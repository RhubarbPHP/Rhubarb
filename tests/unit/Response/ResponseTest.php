<?php

namespace Rhubarb\Crown\Tests\unit\Response;

use Rhubarb\Crown\Response\Response;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class ResponseTest extends RhubarbTestCase
{
    /**
     * @var Response
     */
    protected $response = null;

    protected function setUp()
    {
        $this->response = new Response();

        parent::setUp();
    }

    protected function tearDown()
    {
        $this->response = null;

        parent::tearDown();
    }

    public function testConstructed()
    {
        $this->assertNotNull($this->response, "Failed to instantiate Response object");
    }

    public function testClearHeaders()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->clearHeaders();
        $this->assertCount(0, $this->response->getHeaders(), "Cleared header array was not empty");
    }

    public function testSetAndGetHeader()
    {
        $this->response->clearHeaders();

        $this->response->setHeader('Content-Type', 'application/json');
        $this->assertEquals(
            $this->response->getHeaders(),
            [
                'Content-Type' => 'application/json'
            ],
            "Got headers did not equal set headers"
        );

        $this->response->setHeader('Content-Encoding', 'gzip');
        $this->assertEquals(
            $this->response->getHeaders(),
            [
                'Content-Type' => 'application/json',
                'Content-Encoding' => 'gzip'
            ],
            "Got headers did not equal set headers"
        );
    }

    public function testUnsetHeader()
    {
        $this->response->clearHeaders();

        $this->response->setHeader('Foo', 'Bar');
        $this->response->unsetHeader('Foo');
        $this->assertCount(0, $this->response->getHeaders(), "Failed to unset header");
    }

    public function testDefaultContentTypeHeader()
    {
        $this->assertEquals(
            $this->response->getHeaders()['Content-Type'],
            'text/plain',
            "Content-Type header is not set to text/plain"
        );
    }

    public function testSetContent()
    {
        $string_content = "This is some string content";
        $array_content = ["Key" => "Value"];
        $object_content = new \stdClass();
        $object_content->foo = 'bar';
        $object_content->baz = 1;

        $this->assertNull($this->response->getContent(), "Content hasn't been set yet, should be NULL");

        $this->response->setContent($string_content);
        $this->assertEquals(
            $this->response->getContent(),
            $string_content,
            "Got content did not equal set string content"
        );

        $this->response->setContent($array_content);
        $this->assertEquals(
            $this->response->getContent(),
            $array_content,
            "Got content did not equal set array content"
        );

        $this->response->setContent($object_content);
        $this->assertEquals(
            $this->response->getContent(),
            $object_content,
            "Got content did not equal set object content"
        );
    }

    public function testSend()
    {
        $string_content = "This is some string content";

        $this->response->setContent($string_content);

        ob_start();

        $this->response->send();

        $body = ob_get_clean();

        $this->assertEquals($body, $string_content, "Sent body did not equal set string content");
    }
}
