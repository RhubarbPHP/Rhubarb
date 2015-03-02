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

use Rhubarb\Crown\DataStreams\CsvStream;
use Rhubarb\Crown\Tests\RhubarbTestCase;

class CsvStreamTest extends RhubarbTestCase
{
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		// Create a text file to stream.
		file_put_contents( "cache/unit-test-csv-stream.csv", "a,\"\"b\"\",c
1,\"2,4\",\"3
5\"" );

		// Create a non standards compliant text file to stream.
		file_put_contents( "cache/unit-test-csv-stream-non-rfc.csv", "a,\\\"b\\\",c
1,\"2,\\\"4\",\"3
5\"" );

	}

	public function testStreamReading()
	{
		$stream = new CsvStream( "cache/unit-test-csv-stream.csv" );

		$item = $stream->readNextItem();

		$this->assertEquals( "1", $item[ "a" ] );
		$this->assertEquals( "2,4", $item[ "\"b\"" ] );
		$this->assertEquals( "3
5", $item[ "c" ] );

		$response = $stream->readNextItem();

		$this->assertFalse( $response );
	}

	public function testStreamReadingNonRfcCsv()
	{
		$stream = new CsvStream( "cache/unit-test-csv-stream-non-rfc.csv" );
		$stream->escapeCharacter = "\\";

		$item = $stream->readNextItem();

		$this->assertEquals( "1", $item[ "a" ] );
		$this->assertEquals( "2,\"4", $item[ "\"b\"" ] );
		$this->assertEquals( "3
5", $item[ "c" ] );

		$response = $stream->readNextItem();

		$this->assertFalse( $response );
	}

	public function testStreamWriting()
	{
		$stream = new CsvStream( "cache/unit-test-csv-stream.csv" );
		$stream->appendItem( [
			"a" => "alan",
			"\"b\"" => "ry\"an",
			"c" => "john"
		]);
		$stream->close();

		$content = file_get_contents( "cache/unit-test-csv-stream.csv" );
		$content = str_replace( "\r\n", "\n", $content );

		$this->assertEquals( "a,\"\"b\"\",c\n1,\"2,4\",\"3\n5\"\nalan,\"ry\"\"an\",john", $content );
	}

	public function testStreamWritingNewFile()
	{
		@unlink( "cache/unit-test-csv-stream-new.csv" );

		$stream = new CsvStream( "cache/unit-test-csv-stream-new.csv" );
		$stream->appendItem( [
			"a" => "alan",
			"b" => "ryan",
			"c" => "john"
		]);
		$stream->close();

		$content = file_get_contents( "cache/unit-test-csv-stream-new.csv" );
		$content = str_replace( "\r\n", "\n", $content );

		$this->assertEquals( "a,b,c\nalan,ryan,john", $content );
	}
}