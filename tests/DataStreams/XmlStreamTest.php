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


namespace Rhubarb\Crown\Tests\DataStreams;

use Rhubarb\Crown\DataStreams\XmlStream;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class XmlStreamTest extends RhubarbTestCase
{
    public function testXmlStream()
    {
        file_put_contents("cache/unit-test-xml-stream.xml", '<?xml version="1.0" encoding="ISO-8859-1"?>
<meals>
	<meal>
		<name>Breakfast</name>
	</meal>
	<meal>
		<name>Dinner</name>
	</meal>
	<meal>
		<name>Lunch</name>
	</meal>
</meals>
');

        $stream = new XmlStream("meal", "cache/unit-test-xml-stream.xml");
        $meal = $stream->readNextItem();

        $this->assertEquals("Breakfast", $meal["name"]);

        $stream->readNextItem();
        $stream->readNextItem();
        $meal = $stream->readNextItem();

        $this->assertFalse($meal);

        // This will crash if the file handle isn't released
        //unlink( "cache/unit-test-xml-stream.xml" );
    }
}
 