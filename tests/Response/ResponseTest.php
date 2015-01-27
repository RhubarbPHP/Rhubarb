<?php

namespace Gcd\Tests;

/**
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class ResponseTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
    protected $_response = null;

    protected function setUp()
    {
        $this->_response = new \Rhubarb\Crown\Response\Response();
    }

    protected function tearDown()
    {
        $this->_response = null;
    }

    public function testConstructed()
    {
        $this->assertNotNull($this->_response, "Failed to instantiate Response object");
    }

    public function testClearHeaders()
    {
        $this->_response->SetHeader('Content-Type', 'application/json');
        $this->_response->ClearHeaders();
        $this->assertCount(0, $this->_response->GetHeaders(), "Cleared header array was not empty");
    }

    public function testSetAndGetHeader()
    {
        $this->_response->ClearHeaders();

        $this->_response->SetHeader('Content-Type', 'application/json');
        $this->assertEquals($this->_response->GetHeaders(),
            [
                'Content-Type' => 'application/json'
            ],
            "Got headers did not equal set headers");

        $this->_response->SetHeader('Content-Encoding', 'gzip');
        $this->assertEquals($this->_response->GetHeaders(),
            [
                'Content-Type' => 'application/json',
                'Content-Encoding' => 'gzip'
            ],
            "Got headers did not equal set headers");
    }

    public function testUnsetHeader()
    {
        $this->_response->ClearHeaders();

        $this->_response->SetHeader('Foo', 'Bar');
        $this->_response->UnsetHeader('Foo');
        $this->assertCount(0, $this->_response->GetHeaders(), "Failed to unset header");
    }

    public function testDefaultContentTypeHeader()
    {
        $this->assertEquals($this->_response->GetHeaders()['Content-Type'], 'text/plain',
            "Content-Type header is not set to text/plain");
    }

    public function testSetContent()
    {
        $string_content = "This is some string content";
        $array_content = ["Key" => "Value"];
        $object_content = new \stdClass();
        $object_content->foo = 'bar';
        $object_content->baz = 1;

        $this->assertNull($this->_response->GetContent(), "Content hasn't been set yet, should be NULL");

        $this->_response->SetContent($string_content);
        $this->assertEquals($this->_response->GetContent(), $string_content,
            "Got content did not equal set string content");

        $this->_response->SetContent($array_content);
        $this->assertEquals($this->_response->GetContent(), $array_content,
            "Got content did not equal set array content");

        $this->_response->SetContent($object_content);
        $this->assertEquals($this->_response->GetContent(), $object_content,
            "Got content did not equal set object content");
    }

    public function testSend()
    {
        $string_content = "This is some string content";

        $this->_response->SetContent($string_content);

        ob_start();

        $this->_response->send();

        $body = ob_get_clean();

        $this->assertEquals($body, $string_content, "Sent body did not equal set string content");
    }
}
