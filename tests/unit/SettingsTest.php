<?php

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
