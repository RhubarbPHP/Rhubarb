<?php

namespace Rhubarb\Crown\Tests\unit\Email;

use Rhubarb\Crown\Sendables\Email\EmailAddress;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class EmailAddressTest extends RhubarbTestCase
{
    public function testCreation()
    {
        $emailAddress = new EmailAddress("acuthbert@gcdtech.com");

        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);

        $internetFormat = $emailAddress->GetRfcFormat();

        $this->assertEquals("acuthbert@gcdtech.com", $internetFormat);

        $emailAddress = new EmailAddress("acuthbert@gcdtech.com", "Andrew Cuthbert");

        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $internetFormat = $emailAddress->GetRfcFormat();

        $this->assertEquals("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", $internetFormat);

        $emailAddress = new EmailAddress("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>");
        $this->assertEquals("acuthbert@gcdtech.com", $emailAddress->email);
        $this->assertEquals("Andrew Cuthbert", $emailAddress->name);

        $emailAddress = new EmailAddress("\"Andrew Cuthbert\" <acuthbert@gcdtech.com>", "Mary");

        $this->assertEquals("Mary", $emailAddress->name);
    }
}
