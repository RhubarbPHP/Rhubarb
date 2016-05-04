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

use Rhubarb\Crown\DateTime\RhubarbDateTime;
use Rhubarb\Crown\DateTime\RhubarbTime;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

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
