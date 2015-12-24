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

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\HttpHeaders;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class HttpHeadersTest extends RhubarbTestCase
{
    public function testHeadersAreSet()
    {
        HttpHeaders::clearHeaders();
        HttpHeaders::setHeader("Content-type", "text/plain");

        $headers = HttpHeaders::getHeaders();

        $this->assertCount(1, $headers);
        $this->assertEquals("text/plain", $headers["Content-type"]);

        HttpHeaders::setHeader("Content-length", "2048");

        $headers = HttpHeaders::getHeaders();

        $this->assertCount(2, $headers);
        $this->assertEquals("2048", $headers["Content-length"]);

        HttpHeaders::setHeader("Content-type", "text/xml");

        $headers = HttpHeaders::getHeaders();

        $this->assertEquals("text/xml", $headers["Content-type"]);
    }

    public function testHeadersAreFlushed()
    {
        HttpHeaders::clearHeaders();
        HttpHeaders::setHeader("Content-type", "text/plain");
        HttpHeaders::flushHeaders();

        $headers = HttpHeaders::getHeaders();

        $this->assertTrue(HttpHeaders::$flushed);
    }
}
