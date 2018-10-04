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

namespace Rhubarb\Crown\DateTime;

interface RhubarbDateTimeInterface extends \DateTimeInterface, \JsonSerializable
{
    /**
     * The day constants.
     */
    const SUNDAY    = 0;
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;

    /**
     * Number of X in Y.
     */
    const YEARS_PER_MILLENNIUM = 1000;
    const YEARS_PER_CENTURY = 100;
    const YEARS_PER_DECADE = 10;
    const MONTHS_PER_YEAR = 12;
    const MONTHS_PER_QUARTER = 3;
    const WEEKS_PER_YEAR = 52;
    const WEEKS_PER_MONTH = 4;
    const DAYS_PER_WEEK = 7;
    const HOURS_PER_DAY = 24;
    const MINUTES_PER_HOUR = 60;
    const SECONDS_PER_MINUTE = 60;
    const MICROSECONDS_PER_SECOND = 1000000;

    /**
     * RFC7231 DateTime format.
     *
     * @var string
     */
    const RFC7231_FORMAT = 'D, d M Y H:i:s \G\M\T';

    /**
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    const DEFAULT_TO_STRING_FORMAT = 'd-M-Y';

    /**
     * Format for converting mocked time, includes microseconds.
     *
     * @var string
     */
    const MOCK_DATETIME_FORMAT = 'Y-m-d H:i:s.u';

    /**
     * An invalid date format.
     *
     * @var string
     */
    const INVALID_DATE = "0000-01-01 00:00:00";

    public function year($value);

    public function month($value);

    public function day($value);

    public function hour($value);

    public function minute($value);

    public function second($value);

    public function setDate($year, $month, $day);

    public function setDateTime($year, $month, $day, $hour, $minute, $second = 0);

    public static function parse($time = null, $tz = null);

    public static function createFromFormat($format, $time, $timezone = null);

    public static function previousMonday($referenceDate = null);

    public function applyDateInterval($interval, $unit);

    public function addDays($value);

    public function addDay($value);

    public function subDays($value);

    public function subDay($value);

    public function startOfDay();

    public function endOfDay();

    public function startOfWeek($weekStartsAt = null);

    public function endOfWeek($weekEndsAt = null);

    public function startOfMonth();

    public function endOfMonth();

    public function isWeekday();

    public function isWeekend();

    public function toDateString();

    public function toFormattedDateString();

    public function toTimeString();

    public function toDateTimeString();

    public function toDayDateTimeString();
}