<?php

namespace Rhubarb\Crown\Tests\unit\DateTime;

use Rhubarb\Crown\DateTime\RhubarbDate;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class RhubarbDateTest extends RhubarbTestCase
{
    public function testDateAlwaysHasSameTime()
    {
        $Date = new RhubarbDate("2000-01-01 13:00:00");

        $this->assertEquals("2000-01-01 00:00:00", $Date->format("Y-m-d H:i:s"));

        $Date = new RhubarbDate(new RhubarbDateTime("2000-01-01 10:00:00"));

        $this->assertEquals("2000-01-01 00:00:00", $Date->format("Y-m-d H:i:s"));
    }

    public function testDateAlwaysHasSameDayEvenIfDeveloperSpecifiesDateAfterConstructor()
    {
        $Date = new RhubarbDate("2000-01-01");

        $this->assertEquals("2000-01-01 00:00:00", $Date->format("Y-m-d H:i:s"));

        $Date = new RhubarbDate(new RhubarbDateTime("2000-01-01 10:00:00"));
        $Date->setTime(1, 2, 3);

        $this->assertEquals("2000-01-01 00:00:00", $Date->format("Y-m-d H:i:s"));
    }

    public function testDateIsConsistentNoMatterWhatTimezone()
    {
        date_default_timezone_set("Europe/Berlin");

        $date = new RhubarbDate("2013-12-12");

        $this->assertEquals("2013-12-12", $date->format("Y-m-d"));

        $newDate = new RhubarbDateTime($date);

        $this->assertEquals("2013-12-12", $newDate->format("Y-m-d"));

        date_default_timezone_set("Europe/London");

        // This is a date in BST
        $date = new RhubarbDate("2013-09-24");

        $this->assertEquals("2013-09-24", $date->format("Y-m-d"));
    }
}
