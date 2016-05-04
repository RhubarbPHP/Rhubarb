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

class RhubarbDateInterval extends \DateInterval
{
    public $totalMonths = 0;
    public $totalHours = 0;
    public $totalDays = 0;
    public $totalMinutes = 0;
    public $totalSeconds = 0;
    public $totalWeeks = 0;

    private $monthDays = 0;

    public function __construct($interval_spec)
    {
        parent::__construct($interval_spec);

        $this->calculateTotals();
    }

    public static function createFromDateInterval(\DateInterval $interval)
    {
        $obj = new self('PT0S');

        foreach ($interval as $property => $value) {
            if ($property == "days") {
                if ($value != -99999) {
                    $obj->monthDays = $value;
                }
            } else {
                $obj->$property = $value;
            }
        }

        $obj->calculateTotals();

        return $obj;
    }

    private function calculateTotals()
    {
        $this->totalMonths = ($this->y * 12) + $this->m;

        if ($this->monthDays) {
            $days = $this->monthDays;
        } else {
            $days = $this->d;
        }

        $this->totalDays = $days + (($this->h + ($this->i / 60) + ($this->s / 3600)) / 24);
        $this->totalHours = $this->totalDays * 24;
        $this->totalMinutes = $this->totalDays * 1440;
        $this->totalSeconds = $this->totalDays * 86400;
        $this->totalWeeks = $this->totalDays / 7;
    }
}
