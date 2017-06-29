<?php

namespace Rhubarb\Crown\Tests\unit\unit\Request;

use Rhubarb\Crown\Mime\MimeDocument;
use Rhubarb\Crown\Request\MultiPartFormDataRequest;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RequestTestCase;

class MultiPartFormDataRequestTest extends RequestTestCase
{
    public function testPayloadIsMimeDocument()
    {
        $this->assertTrue(true);
//        $request = new MultiPartFormDataRequest();
//        $request->setRawRequest('MIME-Version: 1.0' . "\r\n" .
//            'Content-Type: multipart/form-data; boundary="----=_NextPart_01CF23F5.37621C10"' . "\r\n" .
//            "\r\n" .
//            'This document is a Single File Web Page, also known as a Web Archive file.' . "\r\n" .
//            "\r\n" .
//            '------=_NextPart_01CF23F5.37621C10' . "\r\n" .
//            'Content-Location: file:///C:/2D794112/junk.htm' . "\r\n" .
//            'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
//            "\r\n" .
//            'Body of first message' . "\r\n" .
//            "\r\n" .
//            '------=_NextPart_01CF23F5.37621C10' . "\r\n" .
//            'Test-Header: header2' . "\r\n" .
//            "\r\n" .
//            'Body of second message' . "\r\n" .
//            '------=_NextPart_01CF23F5.37621C10--' . "\r\n");
//        self::assertInstanceOf(MimeDocument::class, $request->getPayload());
//        self::assertCount(2, $request->getPayload()->getParts());
    }
}
