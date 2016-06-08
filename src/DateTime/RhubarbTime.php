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

use DateTimeZone;

/**
 * Models a time, and always sets the date to be the same.
 */
class RhubarbTime extends RhubarbDateTime
{
    private static $yearMustAlwaysBe = 2000;
    private static $monthMustAlwaysBe = 1;
    private static $dayMustAlwaysBe = 1;

    public function __construct($dateValue = '', DateTimeZone $timezone = null)
    {
        parent::__construct($dateValue, $timezone);

        // Always set the day to the same.
        $this->setDate(self::$yearMustAlwaysBe, self::$monthMustAlwaysBe, self::$dayMustAlwaysBe);
    }

    /**
     * We never want to do comparisons on date - therefore we always force the date time to have the same date
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return \DateTime
     */
    public function setDate($year, $month, $day)
    {
        return parent::setDate(self::$yearMustAlwaysBe, self::$monthMustAlwaysBe, self::$dayMustAlwaysBe);
    }

    function __toString()
    {
        return $this->format("H:i:s");
    }
}
