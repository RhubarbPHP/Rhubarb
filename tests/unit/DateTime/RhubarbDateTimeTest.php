<?php
/**
 * Copyright (c) 2016 RhubarbPHP.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\DateTime;

use Rhubarb\Crown\DateTime\RhubarbDate;
use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class RhubarbDateTimeTest extends RhubarbTestCase
{
    public function testDatesInitaliseProperly()
    {
        $now = time();

        $date = new RhubarbDateTime($now);
        $this->assertEquals(date("Y-m-d", $now), $date->format("Y-m-d"));

        $date = new RhubarbDateTime("2013-09-03");
        $this->assertEquals("2013-09-03", $date->format("Y-m-d"));

        $date = new RhubarbDateTime(new \DateTime("2013-09-03"));
        $this->assertEquals("2013-09-03", $date->format("Y-m-d"));

        $date = new RhubarbDateTime(new \DateTime("0001-01-01"));
        $this->assertEquals("0001-01-01", $date->format("Y-m-d"));

        $date = new RhubarbDateTime("now");
        $this->assertEquals(date("Y-m-d", $now), $date->format("Y-m-d"));

        $date = new RhubarbDateTime("tomorrow");
        $this->assertEquals(date("Y-m-d", $now + 86400), $date->format("Y-m-d"));

    }

    public function testInvalidDates()
    {
        $date = new RhubarbDateTime();

        $this->assertFalse($date->isValidDateTime());

        $date = new RhubarbDateTime("now");

        $this->assertTrue($date->isValidDateTime());

        $date = new RhubarbDateTime("czcvz-23-122");

        $this->assertFalse($date->isValidDateTime());

        $date = new RhubarbDateTime("0000-00-00");

        $this->assertFalse($date->isValidDateTime());

        $date = new RhubarbDateTime("0000-00-00 00:00:00");

        $this->assertFalse($date->isValidDateTime());

        ob_start();

        print $date;

        $string = ob_get_clean();

        $this->assertEquals("", $string);

        $this->assertEquals("", $date->format("Y-m-d"));
    }

    public function testDatePrints()
    {
        $date = new RhubarbDateTime("2013-09-03");

        ob_start();

        print $date;

        $dateString = ob_get_clean();

        $this->assertEquals("03-Sep-2013", $dateString);

        $date = new RhubarbDateTime("abcdefg");

        ob_start();

        print $date;

        $dateString = ob_get_clean();

        $this->assertEquals("", $dateString, "Invalid dates should print an empty string.");
    }

    public function testJsonEncode()
    {
        $date = new RhubarbDateTime("2013-09-03");
        $encoded = json_encode($date);

        $this->assertEquals('"' . $date->format(\DateTime::ISO8601) . '"', $encoded);

        $date = new RhubarbDateTime(str_replace('"', "", $encoded));

        $this->assertEquals("2013-09-03", $date->format("Y-m-d"));
    }

    public function testPreviousMonday()
    {
        $refDate = new RhubarbDate("2014-03-31");

        $newDate = RhubarbDateTime::previousMonday($refDate);

        $this->assertEquals($refDate->format("Ymd"), $newDate->format("Ymd"));

        $refDate = new RhubarbDate("2014-04-02");

        $newDate = RhubarbDateTime::previousMonday($refDate);

        $this->assertEquals("20140331", $newDate->format("Ymd"));

        $refDate = new RhubarbDate("2014-03-30");

        $newDate = RhubarbDateTime::previousMonday($refDate);

        $this->assertEquals("20140324", $newDate->format("Ymd"));
    }
}
