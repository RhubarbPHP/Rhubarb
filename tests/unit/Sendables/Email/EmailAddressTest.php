<?php

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\Sendables\Email\EmailRecipient;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EmailAddressTest extends RhubarbTestCase
{
    public function testCreation()
    {
        $emailAddress = new EmailRecipient("acuthbert@gcdtech.com");

        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);

        $internetFormat = $emailAddress->getRfcFormat();

        $this->assertEquals("acuthbert@gcdtech.com", $internetFormat);

        $emailAddress = new EmailRecipient("acuthbert@gcdtech.com", "Andrew Cuthbert");

        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $internetFormat = $emailAddress->getRfcFormat();

        $this->assertEquals("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", $internetFormat);

        $emailAddress = new EmailRecipient("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>");
        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);
        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $emailAddress = new EmailRecipient("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", "Mary");

        $this->assertEquals("Mary", $emailAddress->name);
    }
}
