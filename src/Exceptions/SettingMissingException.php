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

namespace Rhubarb\Crown\Exceptions;

/**
 *
 * @author acuthbert
 * @copyright GCD Technologies 2012
 */
class SettingMissingException extends RhubarbException
{
    public function __construct($namespace, $settingName)
    {
        parent::__construct("The setting $namespace.$settingName has not been set.");
    }
}
