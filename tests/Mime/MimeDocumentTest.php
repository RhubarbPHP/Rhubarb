<?php

namespace Rhubarb\Crown\Tests\Mime;

use Rhubarb\Crown\Mime\MimeDocument;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class MimeDocumentTest extends RhubarbTestCase
{
	private $message1;

	protected function setUp()
	{
		parent::setUp();

		$this->message1 =  'MIME-Version: 1.0'."\r\n".
			'Content-Type: multipart/related; boundary="----=_NextPart_01CF23F5.37621C10"'."\r\n".
"\r\n".
'This document is a Single File Web Page, also known as a Web Archive file.'."\r\n".
"\r\n".
'------=_NextPart_01CF23F5.37621C10'."\r\n".
'Content-Location: file:///C:/2D794112/junk.htm'."\r\n".
'Content-Transfer-Encoding: quoted-printable'."\r\n".
"\r\n".
'Body of first message'."\r\n".
"\r\n".
'------=_NextPart_01CF23F5.37621C10'."\r\n".
'Test-Header: header2'."\r\n".
"\r\n".
'Body of second message'."\r\n".
'------=_NextPart_01CF23F5.37621C10--'."\r\n";


		$this->message2 = 'MIME-Version: 1.0'."\r\n".
'Content-Type: multipart/related; boundary="----=_NextPart_01CF23F5.37621C11"'."\r\n".
"\r\n".
'------=_NextPart_01CF23F5.37621C11'."\r\n".
'Content-Location: file:///C:/2D794112/junk.htm'."\r\n".
'Content-Transfer-Encoding: quoted-printable'."\r\n".
"\r\n".
'<b:Sources SelectedStyle=3D"\APASixthEditionOfficeOnline.xsl" StyleName=3D"='."\r\n".
'APA" Version=3D"6" xmlns:b=3D"http://schemas.openxmlformats.org/officeDocum='."\r\n".
'ent/2006/bibliography" xmlns=3D"http://schemas.openxmlformats.org/officeDoc='."\r\n".
'ument/2006/bibliography"></b:Sources>'."\r\n".
"\r\n".
'------=_NextPart_01CF23F5.37621C11'."\r\n".
'Test-Header: header2'."\r\n".
"\r\n".
'Body of second message'."\r\n".
"\r\n".
'------=_NextPart_01CF23F5.37621C11'."\r\n".
'Test-Header: header3'."\r\n".
'Content-Type: image/jpeg'."\r\n".
'Content-Transfer-Encoding: base64'."\r\n".
"\r\n".
'VGVzdCENCg0KR2VudGxlIFJlYWRlcjoNCg0KVGhpcyBpcyBub3RoaW5nIG1vcmUgdGhhbiBh'."\r\n".
'IHRlc3QgZmlsZSBjcmVhdGVkIHRvIHByb3ZpZGUgZm9kZGVyIGZvciB0aGUgdmFyaW91cyBl'."\r\n".
'bmNvZGluZyBzY2hlbWVzLiBJZiB5b3UgYXJlIHVzaW5nIGl0IHRvIHRlc3QsIGNvbmdyYXR1'."\r\n".
'bGF0aW9ucyBvbiB5b3VyIGFnaWxpdHkgaW4gY3V0dGluZywgcGFzdGluZywgc2F2aW5nLCBh'."\r\n".
'bmQgZGVjb2RpbmcgdXNpbmcgV2luWmlwLg0KDQpFbmpveSE='."\r\n".
'------=_NextPart_01CF23F5.37621C11--'."\r\n";

	}


	private $message2;

	public function testMimeParses()
	{
		$document = MimeDocument::FromString( $this->message1 );

		$this->assertCount( 2, $document->GetParts() );

		$document = MimeDocument::FromString( $this->message2 );

		$this->assertCount( 3, $document->GetParts() );

		$this->assertEquals( "Body of second message", $document->GetParts()[1]->GetRawBody() );

		$this->assertEquals( "header3", $document->GetParts()[2]->GetHeaders()[ "Test-Header" ] );

		$this->assertEquals( $this->message2, $document->ToString() );
	}

	public function testMimeParsesToConcretePartTypes()
	{
		$document = MimeDocument::FromString( $this->message2 );

		$this->assertInstanceOf( '\Rhubarb\Crown\Mime\MimePartText', $document->GetParts()[0] );
		$this->assertInstanceOf( '\Rhubarb\Crown\Mime\MimePartImage', $document->GetParts()[2] );
	}

	public function testBase64TransferEncoding()
	{
		$document = MimeDocument::FromString( $this->message2 );
		$part = $document->GetParts()[2];
		$text = $part->GetTransformedBody();

		$this->assertEquals( "Test!\r\n".
"\r\n".
"Gentle Reader:\r\n".
"\r\n".
"This is nothing more than a test file created to provide fodder for the various encoding schemes. If you are using it to test, congratulations on your agility in cutting, pasting, saving, and decoding using WinZip.\r\n".
"\r\n".
"Enjoy!", $text );

		$newText = "gibberish is soothing";

		$part->SetTransformedBody( $newText );

		$this->assertEquals( base64_encode( $newText )."\r\n", $part->GetRawBody() );
	}

	public function testQuotedPrintable()
	{
		$document = MimeDocument::FromString( $this->message2 );
		$part = $document->GetParts()[0];
		$text = $part->GetTransformedBody();

		$this->assertEquals( '<b:Sources SelectedStyle="\APASixthEditionOfficeOnline.xsl" StyleName="APA" Version="6" xmlns:b="http://schemas.openxmlformats.org/officeDocument/2006/bibliography" xmlns="http://schemas.openxmlformats.org/officeDocument/2006/bibliography"></b:Sources>', $text );

		$part->SetTransformedBody( "aSDF====rasdfasdfxcv asdf asdf werqwerqsedfasdfasd fasdfasdf asdf asdfasdfas dfasdf asdf a\r\n".
"zxcvasedrf\r\n".
"2q34r2\r\n".
"423423" );

		$this->assertEquals( "aSDF=3D=3D=3D=3Drasdfasdfxcv asdf asdf werqwerqsedfasdfasd fasdfasdf asdf a=\r\n".
"sdfasdfas dfasdf asdf a\r\n".
"zxcvasedrf\r\n".
"2q34r2\r\n".
"423423", $part->GetRawBody() );
	}
}
 