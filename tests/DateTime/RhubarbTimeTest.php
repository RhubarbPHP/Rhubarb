<?php

namespace Rhubarb\Crown\Tests\DateTime;

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\DateTime\RhubarbTime;
use Rhubarb\Crown\Tests\RhubarbTestCase;


class RhubarbTimeTest extends RhubarbTestCase
{
    public function testTimeAlwaysHasSameDay()
    {
        $time = new RhubarbTime("10:00:00");

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));

        $time = new RhubarbTime(new RhubarbDateTime("10:00"));

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));
    }

    public function testTimeAlwaysHasSameDayEvenIfDeveloperSpecifiesDate()
    {
        $time = new RhubarbTime("10:00:00");

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));

        $time = new RhubarbTime(new RhubarbDateTime("2010-01-01 10:00"));

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));
    }

    public function testTimeAlwaysHasSameDayEvenIfDeveloperSpecifiesDateAfterConstructor()
    {
        $time = new RhubarbTime("10:00:00");

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));

        $time = new RhubarbTime(new RhubarbDateTime("2010-01-01 10:00"));
        $time->setDate(2010, 02, 05);

        $this->assertEquals("2000-01-01 10:00:00", $time->format("Y-m-d H:i:s"));
    }

    public function testTimesGetCompared()
    {
        $time1 = new RhubarbTime("17:00:00");
        $time2 = new RhubarbTime("18:00:00");

        $this->assertNotEquals((string)$time1, (string)$time2);
    }
}
