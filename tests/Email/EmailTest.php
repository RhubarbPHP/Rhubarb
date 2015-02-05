<?php

namespace Rhubarb\Crown\Tests\Email;

use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;

class EmailTest extends RhubarbTestCase
{
	public function testEmailRecipients()
	{
		$email = new SimpleEmail();
		$email->AddRecipients(
			[
				"acuthbert@gcdtech.com"
			]
		);

		$this->assertEquals( "acuthbert@gcdtech.com", current( $email->GetRecipients() )->email );

		$email = new SimpleEmail();
		$email->AddRecipients(
			[
				"acuthbert@gcdtech.com",
				"msmith@gcdtech.com"
			]
		);

		$recipients = $email->GetRecipients();
		next( $recipients );

		$this->assertEquals( "acuthbert@gcdtech.com", current( $email->GetRecipients() )->email );

		$email->AddRecipients(
			[
				"acuthbert@gcdtech.com",
				"msmith@gcdtech.com"
			]
		);

		$this->assertCount( 2, $email->GetRecipients(), "Dupes shouldn't get added to recipients twice." );
	}

	public function testFileAttaches()
	{
		$email = new SimpleEmail();
		$email->AddAttachment( "/path/to/file" );

		$this->assertEquals( "/path/to/file", $email->GetAttachments()[0]->path );
		$this->assertEquals( "file", $email->GetAttachments()[0]->name );

		$email->AddAttachment( "/path/to/another-file", "different-name" );

		$this->assertEquals( "/path/to/another-file", $email->GetAttachments()[1]->path );
		$this->assertEquals( "different-name", $email->GetAttachments()[1]->name );
	}

	public function testMimeDocument()
	{
		$email = new SimpleEmail();
		$email->AddRecipient( "acuthbert@gcdtech.com" )
			  ->SetSubject( "Testing" )
			  ->SetSender( "jsmith@gcdtech.com" )
			  ->SetText( "This is test" );

		$this->assertEquals(
			[
				"Content-Type" => "text/plain; charset=utf-8",
				"From" => "jsmith@gcdtech.com",
				"Subject" => "Testing"
			], $email->GetMailHeaders() );

		$this->assertEquals( "This is test", $email->GetBodyRaw() );

		$email->SetHtml( "<p>And some html too!</p>" );

		$this->assertEquals(
			[
				"MIME-Version" => "1.0",
				"Content-Type" => "multipart/alternative; boundary=\"972318020410491600659448730\"",
				"From" => "jsmith@gcdtech.com",
				"Subject" => "Testing"
			], $email->GetMailHeaders() );

		$this->assertEquals( "--972318020410491600659448730\r\n".
"Content-Type: text/plain\r\n".
"Content-Transfer-Encoding: quoted-printable\r\n".
"\r\n".
"This is test\r\n".
"\r\n".
"--972318020410491600659448730\r\n".
"Content-Type: text/html\r\n".
"Content-Transfer-Encoding: quoted-printable\r\n".
"\r\n".
"<p>And some html too!</p>\r\n".
"--972318020410491600659448730--\r\n", $email->GetBodyRaw() );
	}

	public function testEmailSends()
	{
		// Note this test only confirms the email has made it to the provider.
		$email = new SimpleEmail();
		$email->AddRecipient( "acuthbert@gcdtech.com" )
			  ->SetText( "This is a test email" )
			  ->Send();

		$lastEmail = UnitTestingEmailProvider::GetLastEmail();

		$this->assertEquals( $email, $lastEmail );
	}
}
 