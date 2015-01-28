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

namespace Rhubarb\Crown\DateTime;

use DateTime;
use DateTimeZone;

/**
 * Extends the PHP DateTime object by adding a toString() method
 *
 * Incidentally we've prefixed the class name to avoid the easy mistake of using the base class by accident.
 */
class RhubarbDateTime extends \DateTime implements \JsonSerializable
{
    const INVALID_DATE = "0000-01-01 00:00:00";

    public function __construct($dateValue = '', DateTimeZone $timezone = null)
    {
        if ($dateValue instanceof \DateTime) {
            $formattedDate = $dateValue->format("Y-m-d H:i:s");

            if ($formattedDate == "" || $formattedDate == self::INVALID_DATE) {
                parent::__construct(self::INVALID_DATE, $timezone);
            } else {
                parent::__construct("now", $timezone);

                $this->setDate($dateValue->format("Y"), $dateValue->format("m"), $dateValue->format("d"));
                $this->setTime($dateValue->format("H"), $dateValue->format("i"), $dateValue->format("s"));
            }
        } elseif (is_numeric($dateValue)) {
            parent::__construct("now", $timezone);
            $this->setTimestamp($dateValue);
        } else {
            if ($dateValue == "" || $dateValue == "0000-00-00 00:00:00" || $dateValue == "0000-00-00") {
                parent::__construct(self::INVALID_DATE, $timezone);
                return;
            }

            try {
                parent::__construct($dateValue, $timezone);
            } catch (\Exception $er) {
                parent::__construct(self::INVALID_DATE, $timezone);
                return;
            }
        }
    }

    public function diff($datetime2, $absolute = false)
    {
        $interval = parent::diff($datetime2, $absolute);

        return RhubarbDateInterval::createFromDateInterval($interval);
    }

    public function isValidDateTime()
    {
        return (parent::format("Y-m-d H:i:s") != self::INVALID_DATE);
    }

    public function format($format)
    {
        if (!$this->isValidDateTime()) {
            return "";
        }

        return parent::format($format);
    }

    function __toString()
    {
        return $this->format("d-M-Y");
    }

    public function jsonSerialize()
    {
        return $this->format(DateTime::ISO8601);
    }

    /**
     * Returns the date of the Monday of this week.
     *
     * This is often used as a handle on the week commencing date
     *
     * @param RhubarbDateTime $referenceDate The date to find the previous Monday of. Today if null.
     * @return RhubarbDate
     */
    public static function PreviousMonday($referenceDate = null)
    {
        if ($referenceDate == null) {
            $referenceDate = new RhubarbDateTime("today");
        }

        $dow = $referenceDate->format("N");
        $dow--;

        $dow = -$dow;

        $referenceDate->modify($dow . " days");

        return $referenceDate;
    }

    /**
     * Parse a string into a new RhubarbDateTime object according to the specified format
     *
     * @param string $format Format accepted by date().
     * @param string $time String representing the time.
     * @param \DateTimeZone $timezone A DateTimeZone object representing the desired time zone.
     *
     * @return RhubarbDateTime
     */
    public static function createFromFormat($format, $time, $timezone = null)
    {
        if ($timezone == null) {
            $dateTime = DateTime::createFromFormat($format, $time);
        } else {
            $dateTime = DateTime::createFromFormat($format, $time, $timezone);
        }
        return new self($dateTime, $timezone);
    }
}