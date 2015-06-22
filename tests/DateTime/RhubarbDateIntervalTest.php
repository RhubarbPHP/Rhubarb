<?php


namespace Rhubarb\Crown\Tests\DateTime;

use Rhubarb\Crown\DateTime\RhubarbDateInterval;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Tests\RhubarbTestCase;


class RhubarbDateIntervalTest extends RhubarbTestCase
{
    public function testTotals()
    {
        $interval = new RhubarbDateInterval("P1Y");

        $this->assertEquals(0, $interval->m);
        $this->assertEquals(12, $interval->totalMonths);

        $interval = new RhubarbDateInterval("P2Y");
        $this->assertEquals(24, $interval->totalMonths);

        $interval = new RhubarbDateInterval("P2Y2M");
        $this->assertEquals(26, $interval->totalMonths);

        $interval = new RhubarbDateInterval("PT30S");
        $this->assertEquals(30, $interval->totalSeconds);

        $interval = new RhubarbDateInterval("PT30M");
        $this->assertEquals(30, $interval->totalMinutes);

        $interval = new RhubarbDateInterval("PT30M30S");
        $this->assertEquals(30.5, $interval->totalMinutes);

        $interval = new RhubarbDateInterval("PT4H");
        $this->assertEquals(4, $interval->totalHours);

        $interval = new RhubarbDateInterval("PT4H30M");
        $this->assertEquals(4.5, $interval->totalHours);

        $interval = new RhubarbDateInterval("PT4H30M30S");
        $this->assertEquals(4.5083333333333, $interval->totalHours);

        $interval = new RhubarbDateInterval("P2D");
        $this->assertEquals(2, $interval->totalDays);

        $interval = new RhubarbDateInterval("PT12H");
        $this->assertEquals(0.5, $interval->totalDays);

        $interval = new RhubarbDateInterval("P2DT12H");
        $this->assertEquals(2.5, $interval->totalDays);

        $interval = (new RhubarbDateTime("2013-09-01"))->diff(new RhubarbDateTime("2013-08-01"));
        $this->assertEquals(31, $interval->totalDays);

        $interval = new RhubarbDateInterval("P21D");
        $this->assertEquals(3, $interval->totalWeeks);

        $interval = (new RhubarbDateTime("2013-09-01"))->diff(new RhubarbDateTime("2013-07-31"));
        $this->assertEquals(32, $interval->totalDays);

        $interval = (new RhubarbDateTime("2013-09-01"))->diff(new RhubarbDateTime("2013-07-31 12:00:00"));
        $this->assertEquals(31.5, $interval->totalDays);
    }
}
