<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\Mime;

use Rhubarb\Crown\Mime\MimeDocument;
use Rhubarb\Crown\Mime\MimePartImage;
use Rhubarb\Crown\Mime\MimePartText;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class MimeDocumentTest extends RhubarbTestCase
{
    private $message1;

    protected function setUp()
    {
        parent::setUp();

        $this->message1 = 'MIME-Version: 1.0' . "\r\n" .
            'Content-Type: multipart/related; boundary="----=_NextPart_01CF23F5.37621C10"' . "\r\n" .
            "\r\n" .
            'This document is a Single File Web Page, also known as a Web Archive file.' . "\r\n" .
            "\r\n" .
            '------=_NextPart_01CF23F5.37621C10' . "\r\n" .
            'Content-Location: file:///C:/2D794112/junk.htm' . "\r\n" .
            'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
            "\r\n" .
            'Body of first message' . "\r\n" .
            "\r\n" .
            '------=_NextPart_01CF23F5.37621C10' . "\r\n" .
            'Test-Header: header2' . "\r\n" .
            "\r\n" .
            'Body of second message' . "\r\n" .
            '------=_NextPart_01CF23F5.37621C10--' . "\r\n";


        $this->message2 = 'MIME-Version: 1.0' . "\r\n" .
            'Content-Type: multipart/related; boundary="----=_NextPart_01CF23F5.37621C11"' . "\r\n" .
            "\r\n" .
            '------=_NextPart_01CF23F5.37621C11' . "\r\n" .
            'Content-Location: file:///C:/2D794112/junk.htm' . "\r\n" .
            'Content-Transfer-Encoding: quoted-printable' . "\r\n" .
            "\r\n" .
            '<b:Sources SelectedStyle=3D"\APASixthEditionOfficeOnline.xsl" StyleName=3D"=' . "\r\n" .
            'APA" Version=3D"6" xmlns:b=3D"http://schemas.openxmlformats.org/officeDocum=' . "\r\n" .
            'ent/2006/bibliography" xmlns=3D"http://schemas.openxmlformats.org/officeDoc=' . "\r\n" .
            'ument/2006/bibliography"></b:Sources>' . "\r\n" .
            "\r\n" .
            '------=_NextPart_01CF23F5.37621C11' . "\r\n" .
            'Test-Header: header2' . "\r\n" .
            "\r\n" .
            'Body of second message' . "\r\n" .
            "\r\n" .
            '------=_NextPart_01CF23F5.37621C11' . "\r\n" .
            'Test-Header: header3' . "\r\n" .
            'Content-Type: image/jpeg' . "\r\n" .
            'Content-Transfer-Encoding: base64' . "\r\n" .
            "\r\n" .
            'VGVzdCENCg0KR2VudGxlIFJlYWRlcjoNCg0KVGhpcyBpcyBub3RoaW5nIG1vcmUgdGhhbiBh' . "\r\n" .
            'IHRlc3QgZmlsZSBjcmVhdGVkIHRvIHByb3ZpZGUgZm9kZGVyIGZvciB0aGUgdmFyaW91cyBl' . "\r\n" .
            'bmNvZGluZyBzY2hlbWVzLiBJZiB5b3UgYXJlIHVzaW5nIGl0IHRvIHRlc3QsIGNvbmdyYXR1' . "\r\n" .
            'bGF0aW9ucyBvbiB5b3VyIGFnaWxpdHkgaW4gY3V0dGluZywgcGFzdGluZywgc2F2aW5nLCBh' . "\r\n" .
            'bmQgZGVjb2RpbmcgdXNpbmcgV2luWmlwLg0KDQpFbmpveSE=' . "\r\n" .
            '------=_NextPart_01CF23F5.37621C11--' . "\r\n";

    }


    private $message2;

    public function testMimeParses()
    {
        $document = MimeDocument::fromString($this->message1);

        $this->assertCount(2, $document->getParts());

        $document = MimeDocument::fromString($this->message2);

        $this->assertCount(3, $document->getParts());

        $this->assertEquals("Body of second message", $document->getParts()[1]->getRawBody());

        $this->assertEquals("header3", $document->getParts()[2]->getHeaders()["Test-Header"]);

        $this->assertEquals($this->message2, $document->toString());
    }

    public function testMimeParsesToConcretePartTypes()
    {
        $document = MimeDocument::fromString($this->message2);

        $this->assertInstanceOf(MimePartText::class, $document->getParts()[0]);
        $this->assertInstanceOf(MimePartImage::class, $document->getParts()[2]);
    }

    public function testBase64TransferEncoding()
    {
        $document = MimeDocument::fromString($this->message2);
        $part = $document->getParts()[2];
        $text = $part->getTransformedBody();

        $this->assertEquals("Test!\r\n" .
            "\r\n" .
            "Gentle Reader:\r\n" .
            "\r\n" .
            "This is nothing more than a test file created to provide fodder for the various encoding schemes. If you are using it to test, congratulations on your agility in cutting, pasting, saving, and decoding using WinZip.\r\n" .
            "\r\n" .
            "Enjoy!", $text);

        $newText = "gibberish is soothing";

        $part->setTransformedBody($newText);

        $this->assertEquals(base64_encode($newText) . "\r\n", $part->getRawBody());
    }

    public function testQuotedPrintable()
    {
        $document = MimeDocument::fromString($this->message2);
        $part = $document->getParts()[0];
        $text = $part->getTransformedBody();

        $this->assertEquals('<b:Sources SelectedStyle="\APASixthEditionOfficeOnline.xsl" StyleName="APA" Version="6" xmlns:b="http://schemas.openxmlformats.org/officeDocument/2006/bibliography" xmlns="http://schemas.openxmlformats.org/officeDocument/2006/bibliography"></b:Sources>',
            $text);

        $part->setTransformedBody("aSDF====rasdfasdfxcv asdf asdf werqwerqsedfasdfasd fasdfasdf asdf asdfasdfas dfasdf asdf a\r\n" .
            "zxcvasedrf\r\n" .
            "2q34r2\r\n" .
            "423423");

        $this->assertEquals("aSDF=3D=3D=3D=3Drasdfasdfxcv asdf asdf werqwerqsedfasdfasd fasdfasdf asdf a=\r\n" .
            "sdfasdfas dfasdf asdf a\r\n" .
            "zxcvasedrf\r\n" .
            "2q34r2\r\n" .
            "423423", $part->getRawBody());
    }

    public function testBoundaryHeaderQuoting()
    {
        $documentWithQuotedBoundary = MimeDocument::fromString($this->message1);
        $documentWithUnQuotedBoundary = MimeDocument::fromString(preg_replace(
            '#boundary="(.+?)"#',
            'boundary=$1',
            $this->message1
        ));
        self::assertCount(count($documentWithQuotedBoundary->getParts()), $documentWithUnQuotedBoundary->getParts());
    }
}
