<?php

namespace Gcd\Tests;
use Rhubarb\Crown\UnitTesting\UnitTestingSettings;

/**
 * Settings test suite.
 *
 * Note that settings extend Model so we don't need to test all of that plumbing again.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class SettingsTest extends \Rhubarb\Crown\UnitTesting\RhubarbTestCase
{
	public function testSettingsHaveNamespace()
	{
		$settings = new \Rhubarb\Crown\UnitTesting\UnitTestingSettings();
		$this->assertEquals( "UnitTesting", $settings->GetNamespace() );
	}

	public function testSettingsAreSingletons()
	{
		$settings = new \Rhubarb\Crown\UnitTesting\UnitTestingSettings();
		$settings->Foo = "abc";

		$settings = new \Rhubarb\Crown\UnitTesting\UnitTestingSettings();
		$this->assertEquals( "abc", $settings->Foo );

		\Rhubarb\Crown\Settings::DeleteSettingNamespace( "UnitTesting" );

		$settings = new \Rhubarb\Crown\UnitTesting\UnitTestingSettings();
		$this->assertEquals( null, $settings->Foo );
	}

	public function testValuesCanBeAccessedStatically()
	{
		$settings = new \Rhubarb\Crown\UnitTesting\UnitTestingSettings();
		$settings->Foo = "abc";

		$this->assertEquals( "abc", \Rhubarb\Crown\Settings::GetSetting( 'UnitTesting', "Foo" ) );

		// No exception thrown as a default is supplied.
		$return = \Rhubarb\Crown\Settings::GetSetting( 'UnitTesting', "Bar", "123" );
		$this->assertEquals( "123", $return );

		$this->setExpectedException( "\Rhubarb\Crown\Exceptions\SettingMissingException" );
		\Rhubarb\Crown\Settings::GetSetting( 'UnitTesting', "Bar" );
	}

	public function testDefaultsAreSet()
	{
		$settings = new UnitTestingSettings();

		$this->assertEquals( "default", $settings->SettingWithDefault );

		$settings->SettingWithDefault = "abc";

		$settings = new UnitTestingSettings();

		$this->assertEquals( "abc", $settings->SettingWithDefault );
	}
}
