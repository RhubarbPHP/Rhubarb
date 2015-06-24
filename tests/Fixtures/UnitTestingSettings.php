<?php

namespace Rhubarb\Crown\Tests\Fixtures;

use Rhubarb\Crown\Settings;

/**
 * @property string $Foo
 * @property string $Bar
 * @property string $SettingWithDefault
 */
class UnitTestingSettings extends Settings
{
    protected function initialiseDefaultValues()
    {
        parent::initialiseDefaultValues();

        $this->SettingWithDefault = "default";
    }
}
