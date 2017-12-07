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

namespace Rhubarb\Crown\Logging;

use Monolog\Logger;

class MonologLog extends IndentedMessageLog
{

    /**
     * @var Logger $logger
     */
    private $logger;

    public function __construct($logLevel, Logger $logger)
    {
        parent::__construct($logLevel);

        $this->logger = $logger;
    }

    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param int $level The log level
     * @param string $message The text message to log
     * @param string $category The category of log message
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    protected function writeFormattedEntry($level, $message, $category = "", $additionalData)
    {
        $ip = self::getRemoteIP();
        $category = ($category == "") ? "CORE" : $category;

        $message = $category.', '.str_pad($this->uniqueIdentifier, 14, ' ', STR_PAD_LEFT) .
            ',t='.$this->getExecutionTime().
            ',d='.$this->getTimeSinceLastLog().
            ',ip='.$ip.
            ",msg=" . $message;

        switch($level)
        {
            case Log::BULK_DATA_LEVEL:
                $this->logger->addDebug($message, $additionalData);
                break;
            case Log::DEBUG_LEVEL:
                $this->logger->addDebug($message, $additionalData);
                break;
            case Log::ERROR_LEVEL:
                $this->logger->addError($message, $additionalData);
                break;
            case Log::PERFORMANCE_LEVEL:
                $this->logger->addNotice($message, $additionalData);
                break;
            case Log::WARNING_LEVEL:
                $this->logger->addWarning($message, $additionalData);
                break;
            case Log::REPOSITORY_LEVEL:
                $this->logger->addNotice($message, $additionalData);
                break;
            default:
                $this->logger->addNotice($message, $additionalData);
        }
    }
}