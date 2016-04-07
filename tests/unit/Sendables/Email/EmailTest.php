<?php

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\Email\SimpleEmail;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingEmailProvider;

class EmailTest extends RhubarbTestCase
{
    public function testEmailRecipients()
    {
        $email = Container::instance(SimpleEmail::class);
        $email->addRecipientsByEmail(
            [
                "acuthbert@gcdtech.com"
            ]
        );

        $this->assertEquals("acuthbert@gcdtech.com", current($email->getRecipients())->email);

        $email = Container::instance(SimpleEmail::class);
        $email->addRecipientsByEmail(
            [
                "acuthbert@gcdtech.com",
                "msmith@gcdtech.com"
            ]
        );

        $recipients = $email->getRecipients();
        next($recipients);

        $this->assertEquals("acuthbert@gcdtech.com", current($email->getRecipients())->email);

        $email->addRecipientsByEmail(
            [
                "acuthbert@gcdtech.com",
                "msmith@gcdtech.com"
            ]
        );

        $this->assertCount(2, $email->getRecipients(), "Dupes shouldn't get added to recipients twice.");
    }

    public function testFileAttaches()
    {
        $email = Container::instance(SimpleEmail::class);
        $email->AddAttachment("/path/to/file");

        $this->assertEquals("/path/to/file", $email->getAttachments()[0]->path);
        $this->assertEquals("file", $email->getAttachments()[0]->name);

        $email->addAttachment("/path/to/another-file", "different-name");

        $this->assertEquals("/path/to/another-file", $email->getAttachments()[1]->path);
        $this->assertEquals("different-name", $email->getAttachments()[1]->name);
    }

    public function testMimeDocument()
    {
        $email = Container::instance(SimpleEmail::class);
        $email->addRecipientsByEmail("acuthbert@gcdtech.com")
            ->SetSubject("Testing")
            ->SetSender("jsmith@gcdtech.com")
            ->SetText("This is test");

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
        $email = Container::instance(SimpleEmail::class);
        $email->addRecipientsByEmail("acuthbert@gcdtech.com")
            ->SetText("This is a test email")
            ->Send();

        $lastEmail = UnitTestingEmailProvider::getLastEmail();

        $this->assertEquals($email, $lastEmail);
    }

    public function testSimpleEmailExpressAsDictionary()
    {
        $email = new SimpleEmail();
        $email->setSubject( "Hello Richard" );

        $data = $email->toDictionary();

        $this->assertArrayHasKey( "Subject", $data );
        $this->assertEquals($email->getSubject(), $data["Subject"]);
    }

    public function testEmailSendableType()
    {
        $email = new SimpleEmail();
        $type = $email->getSendableType();

        $this->assertEquals("Email", $type);
    }

    public function testToArray()
    {
        $email = new SimpleEmail();
        $email->setSubject("This is a test");
        $email->setSender("alice@bob.com");
        $this->assertReflectionMatches($email);
        $email->addRecipientByEmail("joe@bob.com");
        $this->assertReflectionMatches($email);
        $email->addRecipientByEmail("jane@bob.com", "Jane Bob");
        $this->assertReflectionMatches($email);
        $email->setText("War and peace");
        $this->assertReflectionMatches($email);
        $email->setHtml("<p>War and peace</p>");
        $this->assertReflectionMatches($email);

        file_put_contents("test.txt", "abc123");

        $email->addAttachment("test.txt", "Test File.txt");

        unlink("test.txt");

        $this->assertReflectionMatches($email);
    }

    private function assertReflectionMatches(Email $email)
    {
        $reflection = SimpleEmail::fromArray($email->toArray());

        $this->assertEquals($reflection, $email);
    }
}
