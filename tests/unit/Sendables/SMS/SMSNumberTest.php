<?php

namespace Rhubarb\Crown\Tests\unit\SMS;

use Rhubarb\Crown\Exceptions\SMSException;
use Rhubarb\Crown\Sendables\SMS\SMSNumber;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class SMSNumberTest extends RhubarbTestCase
{
    public function testCreation()
    {
        $smsNumber = new SMSNumber("+447710123123");
        $this->assertEquals("+447710123123", $smsNumber->number);

        $smsNumber = new SMSNumber("+447710123123", "Michael Miscampbell");
        $this->assertEquals("+447710123123", $smsNumber->number);
        $this->assertEquals("Michael Miscampbell", $smsNumber->name);

        try {
            $smsNumber = new SMSNumber("DUMMY DATA");
            $this->fail();
        } catch (SMSException $exception) {
            $this->assertInstanceOf(SMSException::class, $exception);
        }
    }
}
