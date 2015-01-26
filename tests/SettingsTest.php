<?php

namespace Gcd\Tests;
use Gcd\Core\UnitTesting\UnitTestingSettings;

/**
 * Settings test suite.
 *
 * Note that settings extend Model so we don't need to test all of that plumbing again.
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class SettingsTest extends \Gcd\Core\UnitTesting\CoreTestCase
{
	public function testSettingsHaveNamespace()
	{
		$settings = new \Gcd\Core\UnitTesting\UnitTestingSettings();
		$this->assertEquals( "UnitTesting", $settings->GetNamespace() );
	}

	public function testSettingsAreSingletons()
	{
		$settings = new \Gcd\Core\UnitTesting\UnitTestingSettings();
		$settings->Foo = "abc";

		$settings = new \Gcd\Core\UnitTesting\UnitTestingSettings();
		$this->assertEquals( "abc", $settings->Foo );

		\Gcd\Core\Settings::DeleteSettingNamespace( "UnitTesting" );

		$settings = new \Gcd\Core\UnitTesting\UnitTestingSettings();
		$this->assertEquals( null, $settings->Foo );
	}

	public function testValuesCanBeAccessedStatically()
	{
		$settings = new \Gcd\Core\UnitTesting\UnitTestingSettings();
		$settings->Foo = "abc";

		$this->assertEquals( "abc", \Gcd\Core\Settings::GetSetting( 'UnitTesting', "Foo" ) );

		// No exception thrown as a default is supplied.
		$return = \Gcd\Core\Settings::GetSetting( 'UnitTesting', "Bar", "123" );
		$this->assertEquals( "123", $return );

		$this->setExpectedException( "\Gcd\Core\Exceptions\SettingMissingException" );
		\Gcd\Core\Settings::GetSetting( 'UnitTesting', "Bar" );
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
