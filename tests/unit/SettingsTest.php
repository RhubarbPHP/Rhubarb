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
    public function testSettingsHaveNamespace()
    {
        $settings = new UnitTestingSettings();
        $this->assertEquals("UnitTesting", $settings->getNamespace());
    }

    public function testSettingsAreSingletons()
    {
        $settings = new UnitTestingSettings();
        $settings->Foo = "abc";

        $settings = new UnitTestingSettings();
        $this->assertEquals("abc", $settings->Foo);

        Settings::deleteSettingNamespace("UnitTesting");

        $settings = new UnitTestingSettings();
        $this->assertEquals(null, $settings->Foo);
    }

    public function testValuesCanBeAccessedStatically()
    {
        $settings = new UnitTestingSettings();
        $settings->Foo = "abc";

        $this->assertEquals("abc", Settings::getSetting('UnitTesting', "Foo"));

        // No exception thrown as a default is supplied.
        $return = Settings::getSetting('UnitTesting', "Bar", "123");
        $this->assertEquals("123", $return);

        $this->setExpectedException(SettingMissingException::class);
        Settings::getSetting('UnitTesting', "Bar");
    }

    public function testDefaultsAreSet()
    {
        $settings = new UnitTestingSettings();

        $this->assertEquals("default", $settings->SettingWithDefault);

        $settings->SettingWithDefault = "abc";

        $settings = new UnitTestingSettings();

        $this->assertEquals("abc", $settings->SettingWithDefault);
    }
}
