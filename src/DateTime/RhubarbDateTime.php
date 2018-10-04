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
 *
 * Logic usage from the Carbon PHP project version 1.* as licensed under the MIT License as follows:
 *
 * BEGIN CARBON LICENSE
 *
 * <https://github.com/briannesbitt/Carbon/blob/master/LICENSE#L1>
 * Copyright (C) Brian Nesbitt
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 * END CARBON LICENSE
 */

namespace Rhubarb\Crown\DateTime;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Extends the PHP DateTime object by adding a toString() method
 *
 * Incidentally we've prefixed the class name to avoid the easy mistake of using the base class by accident.
 *
 * Usage of logic defined in Carbon 1
 *
 * @property      int $year
 * @property      int $yearIso
 * @property      int $month
 * @property      int $day
 * @property      int $hour
 * @property      int $minute
 * @property      int $second
 * @property-read int $dayOfWeek 0 (for Sunday) through 6 (for Saturday)
 * @property-read int $dayOfWeekIso 1 (for Monday) through 7 (for Sunday)
 * @property-read int $dayOfYear 0 through 365
 * @property-read int $weekOfMonth 1 through 5
 * @property-read int $weekNumberInMonth 1 through 5
 * @property-read int $weekOfYear ISO-8601 week number of year, weeks starting on Monday
 * @property-read int $daysInMonth number of days in the given month
 * @property-read int $quarter the quarter of this instance, 1 - 4
 * @property-read int $offset the timezone offset in seconds from UTC
 * @property-read int $offsetHours the timezone offset in hours from UTC
 * @property-read bool $dst daylight savings time indicator, true if DST, false otherwise
 * @property-read bool $local checks if the timezone is local, true if local, false otherwise
 * @property-read bool $utc checks if the timezone is UTC, true if UTC, false otherwise
 * @property-read string $timezoneName
 * @property-read string $tzName
 * @property-read string $englishDayOfWeek the day of week in English
 * @property-read string $shortEnglishDayOfWeek the abbreviated day of week in English
 * @property-read string $englishMonth the month in English
 * @property-read string $shortEnglishMonth the abbreviated month in English
 * @property-read string $localeDayOfWeek the day of week in current locale LC_TIME
 * @property-read string $shortLocaleDayOfWeek the abbreviated day of week in current locale LC_TIME
 * @property-read string $localeMonth the month in current locale LC_TIME
 * @property-read string $shortLocaleMonth the abbreviated month in current locale LC_TIME
 */
class RhubarbDateTime extends \DateTime implements RhubarbDateTimeInterface
{
    /**
     * First day of week.
     *
     * @var int
     */
    protected static $weekStartsAt = self::MONDAY;

    /**
     * Last day of week.
     *
     * @var int
     */
    protected static $weekEndsAt = self::SUNDAY;

    /**
     * Days of weekend.
     *
     * @var array
     */
    protected static $weekendDays = [
        self::SATURDAY,
        self::SUNDAY
    ];

    public function __construct($dateValue = '', DateTimeZone $timezone = null)
    {
        if ($dateValue instanceof \DateTime) {
            $formattedDate = $dateValue->format("Y-m-d H:i:s");

            if ($formattedDate == "" || $formattedDate == self::INVALID_DATE) {
                parent::__construct(self::INVALID_DATE, $timezone);
            } else {
                parent::__construct("now");

                $this->setTimezone($dateValue->getTimezone());
                $this->setDate($dateValue->format("Y"), $dateValue->format("m"), $dateValue->format("d"));
                $this->setTime($dateValue->format("H"), $dateValue->format("i"), $dateValue->format("s"));

                if ($timezone !== null) {
                    $this->setTimezone($timezone);
                }
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

    /**
     * Get a part of the RhubarbDateTime object
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return string|int|bool|\DateTimeZone
     */
    public function __get($name)
    {
        static $formats = [
            'year'                  => 'Y',
            'yearIso'               => 'o',
            'month'                 => 'n',
            'day'                   => 'j',
            'hour'                  => 'G',
            'minute'                => 'i',
            'second'                => 's',
            'micro'                 => 'u',
            'dayOfWeek'             => 'w',
            'dayOfWeekIso'          => 'N',
            'dayOfYear'             => 'z',
            'weekOfYear'            => 'W',
            'daysInMonth'           => 't',
            'timestamp'             => 'U',
            'englishDayOfWeek'      => 'l',
            'shortEnglishDayOfWeek' => 'D',
            'englishMonth'          => 'F',
            'shortEnglishMonth'     => 'M',
            'localeDayOfWeek'       => '%A',
            'shortLocaleDayOfWeek'  => '%a',
            'localeMonth'           => '%B',
            'shortLocaleMonth'      => '%b',
        ];

        switch (true) {
            case isset($formats[$name]):
                $format = $formats[$name];
                $method = substr($format, 0, 1) === '%' ? 'formatLocalized' : 'format';
                $value = $this->$method($format);

                return is_numeric($value) ? (int) $value : $value;

            case $name === 'weekOfMonth':
                return (int) ceil($this->day / static::DAYS_PER_WEEK);

            case $name === 'weekNumberInMonth':
                return (int) ceil(($this->day + $this->copy()->startOfMonth()->dayOfWeek - 1) / static::DAYS_PER_WEEK);

            case $name === 'quarter':
                return (int) ceil($this->month / static::MONTHS_PER_QUARTER);

            case $name === 'offset':
                return $this->getOffset();

            case $name === 'offsetHours':
                return $this->getOffset() / static::SECONDS_PER_MINUTE / static::MINUTES_PER_HOUR;

            case $name === 'dst':
                return $this->format('I') === '1';

            case $name === 'local':
                return $this->getOffset() === $this->copy()->setTimezone(new DateTimeZone(date_default_timezone_get()))->getOffset();

            case $name === 'utc':
                return $this->getOffset() === 0;

            case $name === 'timezone' || $name === 'tz':
                return $this->getTimezone();

            case $name === 'timezoneName' || $name === 'tzName':
                return $this->getTimezone()->getName();

            default:
                throw new InvalidArgumentException(sprintf("Unknown getter '%s'", $name));
        }
    }

    /**
     * Set a part of the Carbon object
     *
     * @param string                   $name
     * @param string|int|\DateTimeZone $value
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'year':
            case 'month':
            case 'day':
            case 'hour':
            case 'minute':
            case 'second':
                list($year, $month, $day, $hour, $minute, $second) = explode('-', $this->format('Y-n-j-G-i-s'));
                $$name = $value;
                $this->setDateTime($year, $month, $day, $hour, $minute, $second);
                break;

            case 'timestamp':
                parent::setTimestamp($value);
                break;

            case 'timezone':
            case 'tz':
                $this->setTimezone($value);
                break;

            default:
                throw new InvalidArgumentException(sprintf("Unknown setter '%s'", $name));
        }
    }

    /**
     * Get a copy of the instance.
     *
     * @return static
     */
    public function copy()
    {
        return clone $this;
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
        return $this->format(self::DEFAULT_TO_STRING_FORMAT);
    }

    /**
     * Set the instance's year
     *
     * @param int $value
     *
     * @return static
     */
    public function year($value)
    {
        $this->year = $value;

        return $this;
    }

    /**
     * Set the instance's month
     *
     * @param int $value
     *
     * @return static
     */
    public function month($value)
    {
        $this->month = $value;

        return $this;
    }

    /**
     * Set the instance's day
     *
     * @param int $value
     *
     * @return static
     */
    public function day($value)
    {
        $this->day = $value;

        return $this;
    }

    /**
     * Set the instance's hour
     *
     * @param int $value
     *
     * @return static
     */
    public function hour($value)
    {
        $this->hour = $value;

        return $this;
    }

    /**
     * Set the instance's minute
     *
     * @param int $value
     *
     * @return static
     */
    public function minute($value)
    {
        $this->minute = $value;

        return $this;
    }

    /**
     * Set the instance's second
     *
     * @param int $value
     *
     * @return static
     */
    public function second($value)
    {
        $this->second = $value;

        return $this;
    }

    /**
     * Sets the current date of the DateTime object to a different date.
     * Calls modify as a workaround for a php bug
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return static
     *
     * @see https://github.com/briannesbitt/Carbon/issues/539
     * @see https://bugs.php.net/bug.php?id=63863
     */
    public function setDate($year, $month, $day)
    {
        $this->modify('+0 day');

        return parent::setDate($year, $month, $day);
    }

    /**
     * Set the date and time all together
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     *
     * @return static
     */
    public function setDateTime($year, $month, $day, $hour, $minute, $second = 0)
    {
        return $this->setDate($year, $month, $day)->setTime($hour, $minute, $second);
    }

    /**
     * Create a RhubarbDateTime instance from a string.
     *
     * This is an alias for the constructor that allows better fluent syntax
     * as it allows you to do RhubarbDateTime::parse('Monday next week')->fn() rather
     * than (new RhubarbDateTime('Monday next week'))->fn().
     *
     * @param string|null               $time
     * @param \DateTimeZone|string|null $tz
     *
     * @return static
     */
    public static function parse($time = null, $tz = null)
    {
        return new static($time, $tz);
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
     * @return RhubarbDateTime
     */
    public static function previousMonday($referenceDate = null)
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

    /**
     * Applies an $interval of $unit
     *
     * @param int $interval
     * @param string $unit DateInterval unit eg 'Y' for year, 'D' for day
     */
    public function applyDateInterval($interval, $unit)
    {
        if ($interval > 0) {
            $this->add(new RhubarbDateInterval("P{$interval}{$unit}"));
        } elseif ($interval < 0) {
            $interval *= -1;
            $this->sub(new RhubarbDateInterval("P{$interval}{$unit}"));
        }
    }

    /**
     * Add days to the instance. Positive $value travels forward while
     * negative $value travels into the past.
     *
     * @param int $value
     *
     * @return static
     */
    public function addDays($value)
    {
        return $this->modify((int) $value.' day');
    }

    /**
     * Add a day to the instance
     *
     * @param int $value
     *
     * @return static
     */
    public function addDay($value = 1)
    {
        return $this->addDays($value);
    }

    /**
     * Remove days from the instance
     *
     * @param int $value
     *
     * @return static
     */
    public function subDays($value)
    {
        return $this->addDays(-1 * $value);
    }

    /**
     * Remove a day from the instance
     *
     * @param int $value
     *
     * @return static
     */
    public function subDay($value = 1)
    {
        return $this->subDays($value);
    }

    /**
     * Resets the time to 00:00:00 start of day
     *
     * @return static
     */
    public function startOfDay()
    {
        return $this->modify('00:00:00.000000');
    }

    /**
     * Resets the time to 23:59:59 end of day
     *
     * @return static
     */
    public function endOfDay()
    {
        return $this->modify('23.59.59.999999');
    }

    /**
     * Resets the date to the first day of week (defined in $weekStartsAt) and the time to 00:00:00
     *
     * @param int $weekStartsAt optional start allow you to specify the day of week to use to start the week
     *
     * @return static
     */
    public function startOfWeek($weekStartsAt = null)
    {
        $date = $this;
        while ($date->dayOfWeek !== ($weekStartsAt ?? static::$weekStartsAt)) {
            $date = $date->subDay();
        }

        return $date->startOfDay();
    }

    /**
     * Resets the date to end of week (defined in $weekEndsAt) and time to 23:59:59
     *
     * @param int $weekEndsAt optional start allow you to specify the day of week to use to end the week
     *
     * @return static
     */
    public function endOfWeek($weekEndsAt = null)
    {
        $date = $this;
        while ($date->dayOfWeek !== ($weekEndsAt ?? static::$weekEndsAt)) {
            $date = $date->addDay();
        }

        return $date->endOfDay();
    }

    /**
     * Resets the date to the first day of the month and the time to 00:00:00
     *
     * @return static
     */
    public function startOfMonth()
    {
        return $this->setDate($this->year, $this->month, 1)->startOfDay();
    }

    /**
     * Resets the date to end of the month and time to 23:59:59
     *
     * @return static
     */
    public function endOfMonth()
    {
        return $this->setDate($this->year, $this->month, $this->daysInMonth)->endOfDay();
    }

    /**
     * Determines if the instance is a weekday.
     *
     * @return bool
     */
    public function isWeekday()
    {
        return !$this->isWeekend();
    }

    /**
     * Determines if the instance is a weekend day.
     *
     * @return bool
     */
    public function isWeekend()
    {
        return in_array($this->dayOfWeek, static::$weekendDays);
    }

    /**
     * Format the instance as date
     *
     * @return string
     */
    public function toDateString()
    {
        return $this->format('Y-m-d');
    }

    /**
     * Format the instance as a readable date
     *
     * @return string
     */
    public function toFormattedDateString()
    {
        return $this->format('M j, Y');
    }

    /**
     * Format the instance as time
     *
     * @return string
     */
    public function toTimeString()
    {
        return $this->format('H:i:s');
    }

    /**
     * Format the instance as date and time
     *
     * @return string
     */
    public function toDateTimeString()
    {
        return $this->format('Y-m-d H:i:s');
    }

    /**
     * Format the instance with day, date and time
     *
     * @return string
     */
    public function toDayDateTimeString()
    {
        return $this->format('D, M j, Y g:i A');
    }
}
