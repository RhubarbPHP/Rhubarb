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

use Rhubarb\Crown\Logging\Log;

class UnitTestLog extends Log
{
    public $entries = [];

    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param int $level
     * @param string $message The text message to log
     * @param int $indent An indent level - if applicable this can be used to make logs more readable.
     * @param string $category
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function writeEntry($level, $message, $indent, $category = "", $additionalData = [])
    {
        $this->entries[] = [$message, $category, $indent, $additionalData];
    }

    protected function shouldLog($category)
    {
        global $logFilter;  // Yes, it's a global - it's a unit test. Get over it.

        if ($category == "ignore") {
            return false;
        }

        if (isset($logFilter) && $logFilter) {
            return false;
        }

        return parent::shouldLog($category);
    }
}
