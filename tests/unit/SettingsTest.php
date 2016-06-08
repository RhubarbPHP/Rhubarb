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

namespace Rhubarb\Crown\Tests;

use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Settings;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Crown\Tests\Fixtures\UnitTestingSettings;

/**
 * Settings test suite.
 *
 * Note that settings extend Model so we don't need to test all of that plumbing again.
 */
class SettingsTest extends RhubarbTestCase
{
    public function testDefaultsAreSet()
    {
        $settings = UnitTestingSettings::singleton();

        $this->assertEquals("default", $settings->SettingWithDefault);

        $settings->SettingWithDefault = "abc";

        $settings = UnitTestingSettings::singleton();

        $this->assertEquals("abc", $settings->SettingWithDefault);
    }
}
