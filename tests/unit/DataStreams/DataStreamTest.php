<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown\Tests\unit\DataStreams;

use Rhubarb\Crown\DataStreams\CsvStream;
use Rhubarb\Crown\DataStreams\XmlStream;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class DataStreamTest extends RhubarbTestCase
{
    public function testPush()
    {
        file_put_contents("cache/unit-test-xml-stream.xml", '<?xml version="1.0" encoding="ISO-8859-1"?>
<meals>
	<meal>
		<name>Breakfast</name>
		<calories>100</calories>
	</meal>
	<meal>
		<name>Dinner</name>
		<calories>200</calories>
	</meal>
	<meal>
		<name>Lunch</name>
		<calories>300</calories>
	</meal>
</meals>
');

        @unlink("cache/unit-test-csv-output-from-xml.csv");

        $stream = new XmlStream("meal", "cache/unit-test-xml-stream.xml");
        $csvStream = new CsvStream("cache/unit-test-csv-output-from-xml.csv");

        $stream->pushAllItems($csvStream);

        $this->assertFileExists("cache/unit-test-csv-output-from-xml.csv");

        $content = file_get_contents("cache/unit-test-csv-output-from-xml.csv");
        $content = str_replace("\r\n", "\n", $content);

        $this->assertEquals("name,calories\nBreakfast,100\nDinner,200\nLunch,300", $content);
    }
}
