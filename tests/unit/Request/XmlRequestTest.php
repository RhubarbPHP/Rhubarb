<?php

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Request\XmlRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Xml\SimpleXmlTranscoder;

class XmlRequestTest extends RhubarbTestCase
{
    /**
     * @var PhpContext
     */
    private $context;

    protected function setUp()
    {
        parent::setUp();

        $this->context = $this->application->context();
        $this->context->simulateNonCli = true;

        $_SERVER['CONTENT_TYPE'] = 'text/xml';
    }

    public function testRequestType()
    {
        self::assertInstanceOf(XmlRequest::class, $this->application->request());
        $this->setUp();
        $_SERVER['CONTENT_TYPE'] = 'application/xml';
        self::assertInstanceOf(XmlRequest::class, $this->application->request());
    }

    public function testPayload()
    {
        $testPayload =
            [
                'a' => 1,
                'b' => 2
            ];

        $context = $this->application->context();
        $context->simulatedRequestBody = SimpleXmlTranscoder::encode($testPayload);

        $request = $this->application->request();

        $this->assertEquals($testPayload, $request->getPayload());
    }
}
