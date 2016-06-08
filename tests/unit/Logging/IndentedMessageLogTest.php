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

namespace Rhubarb\Crown\Tests\unit\Logging;

use Rhubarb\Crown\Logging\IndentedMessageLog;
use Rhubarb\Crown\Logging\Log;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;

class IndentedMessageLogTest extends RhubarbTestCase
{
    /**
     * @var UnitTestIndentedMessageLog
     */
    private $log;

    protected function setUp()
    {
        parent::setUp();

        Log::clearLogs();
        Log::attachLog($this->log = new UnitTestIndentedMessageLog(Log::DEBUG_LEVEL));
    }

    public function testMessageIsIndented()
    {
        Log::indent();
        Log::debug("Indented Message", "test");

        $this->assertEquals("    Indented Message", $this->log->entries[0][0]);
    }
}

class UnitTestIndentedMessageLog extends IndentedMessageLog
{
    public $entries = [];

    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param string $message The text message to log
     * @param string $category
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function writeFormattedEntry($level, $message, $category = "", $additionalData = [])
    {
        $this->entries[] = [$message, $category, $additionalData];
    }
}
