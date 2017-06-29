<?php

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Mime\MimeDocument;
use Rhubarb\Crown\Request\MultiPartFormDataRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class MultiPartFormDataRequestTest extends RequestTestCase
{
    public function testPayloadIsMimeDocument()
    {
        //  Recording Content Type before request
        $originalContentType = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";

        //  Changing Content Type to MultiPart Form Data
        $_SERVER["CONTENT_TYPE"] = "multipart/form-data; boundary=--=_NextPart_01CF23F5.37621C10";
        $request = new MultiPartFormDataRequest();
        $request->serverData["REQUEST_METHOD"] = 'PUT';

        $request->setUnitTestRequestFile(__DIR__ . '/../../_data/_multiPartFormDataRequestUnitTestFile.txt');
        self::assertTrue(is_array($request->getPayload()));
        self::assertCount(3, $request->getPayload());

        $_SERVER["CONTENT_TYPE"] = $originalContentType;
    }
}
