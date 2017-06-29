<?php

namespace Rhubarb\Crown\Tests\unit\Response;

use Rhubarb\Crown\Response\XmlResponse;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Xml\SimpleXmlTranscoder;

class XmlResponseTest extends RhubarbTestCase
{
    public function testAutomatedFormatting()
    {
        $response = new XmlResponse();
        $response->setContent('<tag>some string content</tag>');
        ob_start();
        $response->send();
        $buffer = ob_get_clean();
        self::assertEquals('<tag>some string content</tag>', $buffer);

        $response->setContent([1,2,3]);
        ob_start();
        $response->send();
        $buffer = ob_get_clean();
        self::assertEquals(SimpleXmlTranscoder::encode([1,2,3]), $buffer);

    }
}
