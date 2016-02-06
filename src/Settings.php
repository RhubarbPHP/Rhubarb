<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Crown;

require_once __DIR__ . "/Modelling/ModelState.php";

use Rhubarb\Crown\DependencyInjection\Container;
use Rhubarb\Crown\DependencyInjection\SingletonInterface;
use Rhubarb\Crown\DependencyInjection\SingletonTrait;
use Rhubarb\Crown\Exceptions\SettingMissingException;
use Rhubarb\Crown\Modelling\ModelState;

/**
 * A base class for creating settings classes.
 */
abstract class Settings implements SingletonInterface
{
    use SingletonTrait;

    private $needsInitialised = true;

    protected function __construct()
    {
        if ($this->needsInitialised) {
            $this->needsInitialised = false;
            $this->initialiseDefaultValues();
        }
    }

    /**
     * Override this class to set default values for settings.
     */
    protected function initialiseDefaultValues()
    {

    }
}
