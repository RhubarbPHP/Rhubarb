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

namespace Rhubarb\Crown\Exceptions;

// This include is required as Implementation errors can be thrown before auto loaders are registered.
include_once(__DIR__ . "/RhubarbException.php");

/**
 * Thrown normally if the extender of a class does not implement the base class properly.
 */
class ImplementationException extends RhubarbException
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}
