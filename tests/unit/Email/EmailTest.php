<?php

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;

class EmailTest extends RhubarbTestCase
{
    public function testEmailRecipients()
    {
        $email = new SimpleEmail();
        $email->addRecipients(
            [
                "acuthbert@gcdtech.com"
            ]
        );

        $this->assertEquals("acuthbert@gcdtech.com", current($email->getRecipients())->email);

        $email = new SimpleEmail();
        $email->addRecipients(
            [
                "acuthbert@gcdtech.com",
                "msmith@gcdtech.com"
            ]
        );

        $recipients = $email->getRecipients();
        next($recipients);

        $this->assertEquals("acuthbert@gcdtech.com", current($email->getRecipients())->email);

        $email->addRecipients(
            [
                "acuthbert@gcdtech.com",
                "msmith@gcdtech.com"
            ]
        );

        $this->assertCount(2, $email->getRecipients(), "Dupes shouldn't get added to recipients twice.");
    }

    public function testFileAttaches()
    {
        $email = new SimpleEmail();
        $email->addAttachment("/path/to/file");

        $this->assertEquals("/path/to/file", $email->getAttachments()[0]->path);
        $this->assertEquals("file", $email->getAttachments()[0]->name);

        $email->addAttachment("/path/to/another-file", "different-name");

        $this->assertEquals("/path/to/another-file", $email->getAttachments()[1]->path);
        $this->assertEquals("different-name", $email->getAttachments()[1]->name);
    }

    public function testMimeDocument()
    {
        $email = new SimpleEmail();
        $email->addRecipient("acuthbert@gcdtech.com")
            ->setSubject("Testing")
            ->setSender("jsmith@gcdtech.com")
            ->setText("This is test");

        $this->assertEquals(
            [
                "Content-Type" => "text/plain; charset=utf-8",
                "From" => "jsmith@gcdtech.com",
                "Subject" => "Testing"
            ],
            $email->getMailHeaders()
        );

        $this->assertEquals("This is test", $email->getBodyRaw());

        $email->setHtml("<p>And some html too!</p>");

        $this->assertEquals(
            [
                "MIME-Version" => "1.0",
                "Content-Type" => "multipart/alternative; boundary=\"972318020410491600659448730\"",
                "From" => "jsmith@gcdtech.com",
                "Subject" => "Testing"
            ],
            $email->getMailHeaders()
        );

        $this->assertEquals("--972318020410491600659448730\r\n" .
            "Content-Type: text/plain\r\n" .
            "Content-Transfer-Encoding: quoted-printable\r\n" .
            "\r\n" .
            "This is test\r\n" .
            "\r\n" .
            "--972318020410491600659448730\r\n" .
            "Content-Type: text/html\r\n" .
            "Content-Transfer-Encoding: quoted-printable\r\n" .
            "\r\n" .
            "<p>And some html too!</p>\r\n" .
            "--972318020410491600659448730--\r\n", $email->getBodyRaw());
    }

    public function testEmailSends()
    {
        // Note this test only confirms the email has made it to the provider.
        $email = new SimpleEmail();
        $email->addRecipient("acuthbert@gcdtech.com")
            ->setText("This is a test email")
            ->send();

        $lastEmail = UnitTestingEmailProvider::getLastEmail();

        $this->assertEquals($email, $lastEmail);
    }
}
