<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Settings;

/**
 *
 * @property string $Foo
 * @property string $Bar
 * @property string $SettingWithDefault
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class UnitTestingSettings extends Settings
{
	protected function InitialiseDefaultValues()
	{
		parent::InitialiseDefaultValues();

		$this->SettingWithDefault = "default";
	}
}
