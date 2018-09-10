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

/**
 * An abstract base class to support logging
 *
 * Note that this logging toolkit is not fully object orientated (no LogEntry class for example)
 * as this needs to be as fast as possible and so we rely on passing primitive data types only
 * and use static getters for things like IP address etc.
 *
 */
abstract class Log
{
    /**
     * Used for unrecoverable errors such as unhandled exceptions. The application didn't know what
     * to do and the story didn't end well for the user.
     */
    const ERROR_LEVEL = 1;

    /**
     * Use for issues that we should draw the developers attention to, even if it has been handled
     * by the application in some way and with no negative effect on the end user. For example if an
     * image cache file couldn't be created, the image could still be displayed but performance might
     * be impaired.
     */
    const WARNING_LEVEL = 2;

    /**
     * Use for statements that might assist in debugging complicated code paths and decision logic.
     */
    const DEBUG_LEVEL = 4;

    /**
     * Use for monitoring repository connections (e.g. database queries)
     */
    const REPOSITORY_LEVEL = 8;

    /**
     * Use to log actual data packages, for example API requests and responses.
     *
     * Normally this requires a special log implementation - this would not be appropriate for
     * the PhpLog class for instance.
     */
    const BULK_DATA_LEVEL = 16;

    /**
     * Use to log performance related information.
     */
    const PERFORMANCE_LEVEL = 32;

    /**
     * Use to log general information - not as noisy as debug, but important to be logged somewhere.
     */
    const INFORMATION_LEVEL = 64;

    const ALL = 255;

    /**
     * The current indent level.
     *
     * This acts as a very loose indicator of code paths - it can easily go wrong if the out dent and
     * indent functions are not balanced properly.
     *
     * @var int
     */
    private static $indentLevel = 0;

    protected $levelMask = 0;

    /**
     * The collection of log engines attached to the application.
     *
     * @var Log[]
     */
    private static $logs = [];

    private static $loggingEnabled = true;

    protected $uniqueIdentifier;

    protected $startTime;

    protected $lastLogTime;

    public function __construct($logLevel)
    {
        $this->levelMask = $logLevel;
        $this->uniqueIdentifier = uniqid();
        $this->startTime = $this->lastLogTime = microtime(true);
    }

    /**
     * Change the level mask which controls when this log is engaged.
     *
     * e.g. SetLevelMask( Log::DEBUG_LEVEL )
     *
     * @param $newMask
     */
    public function setLevelMask($newMask)
    {
        $this->levelMask = $newMask;
    }

    /**
     * Reenables logging if it was disabled.
     */
    public static function enableLogging()
    {
        self::$loggingEnabled = true;
    }

    /**
     * Disables all logging until reversed by calling EnableLogging()
     *
     * Sometimes this is necessary to stop the action of logging triggering a further log
     * entry and causing an infinite loop.
     */
    public static function disableLogging()
    {
        self::$loggingEnabled = false;
    }

    /**
     * Attachs a new log to the application.
     *
     * @param Log $log
     */
    public static function attachLog(Log $log)
    {
        self::$logs[] = $log;
    }

    /**
     * Detaches all logs from the application.
     */
    public static function clearLogs()
    {
        self::$logs = [];
        self::$indentLevel = 0;
    }

    protected static function getPhpSessionID()
    {
        return session_id();
    }

    protected static function getRemoteIP()
    {
        return (isset($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : "None";
    }

    /**
     * Performs a mask to see if this log is appropriate for the given log level.
     *
     * @param $level
     * @return bool
     */
    protected function appropriateFor($level)
    {
        return (($level & $this->levelMask) > 0);
    }

    /**
     * Override this to provide custom control over whether the log should engage or not.
     *
     * @param string $category The category of the log message
     * @return bool
     */
    protected function shouldLog($category)
    {
        return true;
    }

    protected function getExecutionTime()
    {
        return round((microtime(true) - $this->startTime) * 1000, 1);
    }

    protected function getTimeSinceLastLog()
    {
        return round((microtime(true) - $this->lastLogTime) * 1000, 1);
    }

    /**
     * The logger should implement this method to perform the actual log committal.
     *
     * @param int $level The log level this message was raised at.
     * @param string $message The text message to log
     * @param int $indent An indent level - if applicable this can be used to make logs more readable.
     * @param string $category The category of log message
     * @param array $additionalData Any number of additional key value pairs which can be understood by specific
     *                                  logs (e.g. an API log might understand what AuthenticationToken means)
     * @return mixed
     */
    abstract protected function writeEntry($level, $message, $indent, $category = "", $additionalData = []);

    public static function createEntry($level, $message, $category = "", $additionalData = [])
    {
        if (!self::$loggingEnabled) {
            return;
        }

        foreach (self::$logs as $log) {
            if ($log->appropriateFor($level) && $log->shouldLog($category)) {
                // If the $message is actually a call back function we can go ahead and run it now
                // that we know we will need it's values.
                if (is_callable($message)) {
                    $result = $message();

                    if (is_array($result)) {
                        $message = $result[0];

                        if (sizeof($result) > 1) {
                            $additionalData = $result[1];
                        }
                    } else {
                        $message = $result;
                    }
                }

                $log->writeEntry($level, $message, self::$indentLevel, $category, $additionalData);
                $log->lastLogTime = microtime(true);
            }
        }
    }

    public static function debug($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::DEBUG_LEVEL, $message, $category, $additionalData);
    }

    public static function error($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::ERROR_LEVEL, $message, $category, $additionalData);
    }

    public static function warning($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::WARNING_LEVEL, $message, $category, $additionalData);
    }

    public static function repository($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::REPOSITORY_LEVEL, $message, $category, $additionalData);
    }

    public static function performance($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::PERFORMANCE_LEVEL, $message, $category, $additionalData);
    }

    public static function info($message, $category = "", $additionalData = [])
    {
        self::createEntry(self::INFORMATION_LEVEL, $message, $category, $additionalData);
    }

    public static function bulkData($message, $category = "", $data = "")
    {
        self::createEntry(self::BULK_DATA_LEVEL, $message, $category, $data);
    }

    public static function indent()
    {
        self::$indentLevel++;
    }

    public static function outdent()
    {
        self::$indentLevel--;
    }
}
