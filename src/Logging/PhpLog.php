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

namespace Rhubarb\Crown\Logging;

require_once __DIR__ . "/IndentedMessageLog.php";

/**
 * A log implementation which outputs to the standard php error log
 */
class PhpLog extends IndentedMessageLog
{
    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param string $message The text message to log
     * @param string $category The category of log message
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function writeFormattedEntry($message, $category = "", $additionalData)
    {
        $ip = self::getRemoteIP();
        $category = ($category == "") ? "CORE" : $category;

        error_log($category .
            str_pad($this->uniqueIdentifier, 14, ' ', STR_PAD_LEFT) .
            str_pad($this->GetExecutionTime(), 7, ' ', STR_PAD_LEFT) .
            str_pad($this->GetTimeSinceLastLog(), 7, ' ', STR_PAD_LEFT) .
            str_pad($ip, 16, ' ', STR_PAD_LEFT) .
            " " . $message);
    }
}