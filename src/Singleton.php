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

/**
 * An implementation of the singleton pattern.
 *
 * @package Core
 */
class Singleton
{
    private static $singleton = null;

    public static function getSingleton()
    {
        if (self::$singleton == null) {
            $class = get_called_class();
            self::$singleton = new $class();
        }

        return self::$singleton;
    }
}
