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

namespace Rhubarb\Crown\Tests\unit\Logging;

use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class LogTest extends RhubarbTestCase
{
    /**
     * @var UnitTestLog
     */
    private $log;

    protected function setUp()
    {
        parent::setUp();

        Log::clearLogs();
        Log::attachLog($this->log = new UnitTestLog(Log::ERROR_LEVEL));
    }

    public function testLogGetsHit()
    {
        $this->log->setLevelMask(Log::DEBUG_LEVEL);

        Log::Debug("This is a test", "test", ["a" => "b"]);

        $this->assertEquals(["This is a test", "test", 0, ["a" => "b"]], $this->log->entries[0]);
    }

    public function testLogGetsHitWhenCorrectLevel()
    {
        Log::debug("This is a test", "test", ["a" => "b"]);

        $this->assertCount(0, $this->log->entries);
    }

    public function testLogCanUseAClosure()
    {
        Log::error(function () {
            return ["Message", ["b" => "c"]];
        }, "test");

        $this->assertEquals(["Message", "test", 0, ["b" => "c"]], $this->log->entries[0]);

        Log::error(function () {
            return "Message 2";
        }, "test");

        $this->assertEquals(["Message 2", "test", 0, []], $this->log->entries[1]);
    }

    public function testLogCanIndent()
    {
        Log::indent();
        Log::error("This is a test", "test");

        $this->assertEquals(1, $this->log->entries[0][2]);

        Log::indent();
        Log::error("This is a test", "test");

        $this->assertEquals(2, $this->log->entries[1][2]);

        Log::outdent();
        Log::error("This is a test", "test");

        $this->assertEquals(1, $this->log->entries[2][2]);
    }

    public function testLogCanHaveCustomFilter()
    {
        global $logFilter;

        Log::error("This is a test we should not see", "ignore");
        $this->assertCount(0, $this->log->entries);

        $logFilter = true;

        Log::error("This is a test we should not see", "test");
        $this->assertCount(0, $this->log->entries);
    }
}
